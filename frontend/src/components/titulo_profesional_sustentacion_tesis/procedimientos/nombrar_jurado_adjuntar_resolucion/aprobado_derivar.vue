<template>
    <b-card>
        <template v-if="!existeRecursoRutaVecinas">
            <b-card no-body>
                <b-tabs 
                    v-model="tabIndex" 
                    card        
                    active-nav-item-class="font-weight-bold text-uppercase text-danger"   
                    style="min-height: 250px"                        
                >   
                    <b-tab title="1. Asignar jurado" title-item-class="disabledTab" :disabled="tabIndex2 < 0">
                        <jurados
                            :expediente="expediente"
                            :idgrado_proc="grado_procedimiento.id"
                            :idusuario="usuario.id"                                                
                            :ruta="ruta"  
                            ref="jurados"     
                        />  
                        <div v-if="errors.length" class="alert alert-danger" role="alert">
                            <ul>
                                <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
                            </ul>
                        </div>      
                    </b-tab>
                    <b-tab title="2. Generar documento" title-item-class="disabledTab" :disabled="tabIndex2 < 1">
                        <generacion_documento                                        
                            :expediente="expediente"  
                            :graduando="graduando"                           
                            :asesor="asesor" 
                            :jurados="array_jurado_asignado"                                                                              
                            nombre_archivo_pdf="resolucion_designacion_jurado.php"
                            boton_nombre="Resolución designación jurado"
                            ref="documentos"
                        />                      
                    </b-tab>  
                    <b-tab title="3. Añadir documento" title-item-class="disabledTab" :disabled="tabIndex2 < 2">
                        <documentos               
                            :expediente="expediente"
                            :idgrado_proc="grado_procedimiento.id"
                            :idusuario="usuario.id"                                                                    
                            :ruta="ruta"                                                           
                            ref="documentos"
                            max_docs = "1"
                            nombre_asignado = "Resolución de designación de jurado"
                        />
                        <div v-if="errors.length" class="alert alert-danger" role="alert">
                            <ul><li v-for="(error, i) in errors" :key="i">{{ error }}</li></ul>
                        </div>       
                    </b-tab>                   
                    <b-tab :title="'4. '+ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1)+' expediente'" 
                        title-item-class="disabledTab" :disabled="tabIndex2 < 3">
                        <movimiento_expediente
                            :grado_modalidad="grado_modalidad"
                            :grado_procedimiento="grado_procedimiento"                                                    
                            :usuario="usuario"                                                       
                            :expediente="expediente"
                            :movimiento="movimiento"
                            :ruta="ruta"                                                            
                        />
                    </b-tab>
                </b-tabs>
            </b-card>        
            <div class="text-center">
                <b-button-group class="mt-3">
                    <b-button class="mr-1" @click="prevTab" :disabled="tabIndex == 0">Anterior</b-button>
                    <b-button @click="nextTab" :disabled="tabIndex == 3">Siguiente</b-button>
                </b-button-group>     
            </div> 
        </template>
        <template v-else>
            <div class="alert alert-danger" role="alert">
                <ul><li>Debe deshacer las acciones realizadas en otras opciones de este procedimiento</li></ul>
            </div>                                                                 
        </template>
    </b-card>       
</template>
<script>
import jurados from '../../recursos/jurados.vue'
import generacion_documento from '../../recursos/generacion_documento.vue'
import documentos from '../../recursos/documentos.vue'
import movimiento_expediente from '../../recursos/movimiento_expediente.vue'

export default {
    name: 'derivado-aprobar',
    props: {                      
        expediente: Object,
        graduando: Object,        
        ruta: Object,
        movimiento: Object
    },
    components: {    
        jurados,
        generacion_documento,
        documentos,
        movimiento_expediente,           
    },
    data() {
        return {             
            url: this.$root.API_URL,     
            usuario: this.$store.getters.getUsuario,
            grado_modalidad: this.$store.getters.getGradoModalidad,
            grado_procedimiento: this.$store.getters.getGradoProcedimiento,                  
            tabIndex: 0,         
            tabIndex2: 0, 
            array_jurado_asignado : [],            
            asesor : null,  //object                                         
            errors: [], 
        }
    },
    computed: {
        existeRecursoRutaVecinas() {
            return this.$store.state.rutaVecinaActiva
        }
    },
    created() {                          
        this.$store.dispatch("verificarRecursoRutasVecinas", this.ruta.id);           
        //this.getJuradosAsignados()                   
        this.getAsesor()          
    },    
    methods: {            
        prevTab() {
            this.errors = [] 
            this.tabIndex2--       
            this.tabIndex--        
        },  
        nextTab() {      
            this.errors = [] 
            let pasar = false              
                            
            if (this.tabIndex == 0) {
                pasar = this.validarTab1()
            }         

            if (this.tabIndex == 1) {
                pasar = true
            }         
            
            if (this.tabIndex == 2) {
                pasar = this.validarTab3()
            }         

            if (pasar) {
                this.tabIndex2++
                this.$nextTick(function () {
                    this.tabIndex++        
                })  
            }              
        },   
        validarTab1() {        
            if (this.$refs.jurados.cantidadJurados() < 3) { //referencia al metodo del componente hijo
                this.errors.push("Debe agregar 03 integrantes para el jurado.")
            }                        

            if (!this.errors.length) {
                this.getJuradosAsignados() //obtener los jurados para el pdf 
                return true
            }      

            return false
        },
        validarTab3() {        
            if (this.$refs.documentos.cantidadDocumentos() == 0) { //referencia al metodo del componente hijo
                this.errors.push("Debe registrar documentos para el expediente seleccionado.")
            }                        

            if (!this.errors.length) {
                return true
            }      

            return false
        },        
        getAsesor() {
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id) 

            this.axios.post(`${this.url}/Persona/get_asesor_expediente`, formData)
            .then(response => {                
                if (!response.data.error) {                
                    this.asesor = response.data.asesor
                }
                else {                
                    console.log(response.data.message)      
                }
            })    
        }, 
        getJuradosAsignados() {            
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)
            formData.append('idgrado_proc', this.grado_procedimiento.id)
            formData.append('idusuario', this.usuario.id)            

            this.axios.post(`${this.url}/Persona/jurado_asignado_expediente`, formData)
            .then(response => {            
                if (!response.data.error) {
                    this.array_jurado_asignado = response.data.array_jurado_asignado                                        
                }
                else {
                    console.log(response.data.message)      
                }
            })   
        },                                      
    },     
}
</script>
<style scoped>
    ul {
        margin-bottom: 0px;    
    }         
</style>
<style>
    .disabledTab{
        pointer-events: none;
    }          
</style>