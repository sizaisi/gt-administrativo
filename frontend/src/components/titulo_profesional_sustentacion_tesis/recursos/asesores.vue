<template>
    <div>    
        <b-form @submit.prevent="registrarAsesor" class="mb-3">
            <b-row class="justify-content-lg-center">
                <b-col col lg="6">
                    <v-select   
                        v-model="docente_seleccionado"
                        :options="array_docente"                                              
                        :reduce="docente => docente" 
                        label="apn"
                        class="style-chooser"
                        placeholder="-- Elija un asesor --"                                                                                                             
                    > 
                        <template #search="{attributes, events}">
                            <input
                                class="vs__search"
                                :required="!docente_seleccionado"
                                v-bind="attributes"
                                v-on="events"
                            />
                        </template>
                        <template slot="no-options">
                            Opción no encontrada...
                        </template>
                    </v-select>
                </b-col>                    
                <b-col col lg="2">
                    <b-button style="height:34px" variant="success" size="sm" type="submit" title="Añadir" :disabled="asesor!=null">
                        <b-icon icon="plus-circle"></b-icon> Asignar
                    </b-button>
                </b-col>                    
            </b-row>  
        </b-form>        
        <table class="table table-bordered table-sm" v-if="asesor != null">   
            <thead>
                <th class="text-center">Nro. Documento</th> 
                <th class="text-left">Docente</th> 
                <th class="text-center">Tipo</th>                                                               
                <th class="text-center">Eliminar</th>
            </thead>
            <tbody>
                <tr>                              
                <td class="text-center" v-text="asesor.nro_documento"></td>
                <td class="text-left" v-text="asesor.apn"></td>
                <td class="text-center" v-text="asesor.tipo"></td>                                
                <td class="text-center">
                    <b-button variant="danger" size="sm" title="Eliminar asesor" @click="eliminarAsesor(asesor.id)">
                        <b-icon icon="trash"></b-icon>
                    </b-button>
                </td>                                                            
                </tr>                                                
            </tbody>
        </table>  
        <div v-if="errors.length" class="alert alert-danger" role="alert">
            <ul><li v-for="(error, i) in errors" :key="i">{{ error }}</li></ul>
        </div>                       
    </div>    
</template>

<script>
export default {
    name: 'asesores',
    props: {        
        expediente: Object,
        idgrado_proc: String,    
        idusuario: String,                        
        ruta: Object,        
    },
    data() {
        return {             
            url: this.$root.API_URL,                                         
            asesor_anterior : null,  //object
            asesor : null,  //object 
            docente_seleccionado : null,  //object              
            array_docente : [],
            array_asesores_ant: [], //asesores anteriores
            errors: [],                                                                      
        }
    },
    created() {          
        this.getCandidatosAsesores()                 
        this.getAsesoresAnteriores()         
        this.getAsesor()           
    },
    methods: {     
        existeAsesor() {
            if (this.asesor != null) {
                return true
            }
            
            return false
        },                
        getCandidatosAsesores() {            
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)            

            this.axios.post(`${this.url}/Usuario/getDocentes`, formData)
            .then(response => {                
                if (!response.data.error) {
                    this.array_docente = response.data.array_docente                                                                                                
                }
                else {
                    console.log(response.data.message)
                }
            })
        },
        getAsesoresAnteriores() {
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)                        

            this.axios.post(`${this.url}/Persona/get_all_asesores`, formData)
            .then(response => {                
                if (!response.data.error) {                
                    this.array_asesores_ant = response.data.array_asesor                    
                }
                else {                
                    console.log(response.data.message)      
                }
            })    
        },  
        getAsesor() {
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)                        
            formData.append('idgrado_proc', this.idgrado_proc)
            formData.append('idusuario', this.idusuario)                                   
            
            this.axios.post(`${this.url}/Persona/get_asesor`, formData)
            .then(response => {                 
                if (!response.data.error) {                
                    this.asesor = response.data.asesor
                }
                else {                
                    console.log(response.data.message)      
                }
            })    
        },          
        encontrarAsesorAnterior(iddocente) {
            let obj = this.array_asesores_ant.find(x => x.iddocente === iddocente);
            let index = this.array_asesores_ant.indexOf(obj);

            if (index != -1) {                
                return true                
            }

            return false
        },
        registrarAsesor() {       
            this.errors = []

            if (this.array_asesores_ant.length > 0 && this.encontrarAsesorAnterior(this.docente_seleccionado.id)) {              
              this.errors.push("El docente seleccionado denego la asesoria del expediente seleccionado.")
              return
            } 

            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)                        
            formData.append('idgrado_proc', this.idgrado_proc)
            formData.append('idusuario', this.idusuario)   
            formData.append('idruta', this.ruta.id)   
            formData.append('iddocente', this.docente_seleccionado.id)                                   
            formData.append('tipo', 'asesor')                                   
            
            this.axios.post(`${this.url}/Persona/store`, formData)
                .then(response => {                                                   
                    this.resetearValores()                                   
                    if (!response.data.error) {                        
                        this.$root.mostrarNotificacion('Éxito!', 'success', 4000, 'done', response.data.message, 'bottom-right')
                        this.getAsesor()                           
                    }
                    else {
                        this.$root.mostrarNotificacion('Error!', 'danger', 4000, 'error_outline', response.data.message, 'bottom-right')                                                          
                    }                      
                })      
        },
        eliminarAsesor(id) {        
            let formData = new FormData()
            formData.append('id', id)                        
            formData.append('idexpediente', this.expediente.id)
            formData.append('idgrado_proc', this.idgrado_proc)
            formData.append('tipo', 'asesor')                  
            
            this.$bvModal.msgBoxConfirm(
                '¿Esta seguro de eliminar el asesor seleccionado?', {
                title: 'Eliminar asesor',                    
                okVariant: 'danger',
                okTitle: 'SI',
                cancelTitle: 'NO',          
                centered: true
            })
            .then(value => {
                if (value) {        
                    this.axios.post(`${this.url}/Persona/delete`, formData)
                    .then(response => {                                                   
                        this.resetearValores()  
                        this.asesor = null
                        if (!response.data.error) {
                            this.$root.mostrarNotificacion('Éxito!', 'success', 4000, 'done', response.data.message, 'bottom-right')
                            this.getAsesor()                            
                        }
                        else {
                            this.$root.mostrarNotificacion('Error!', 'danger', 4000, 'error_outline', response.data.message, 'bottom-right')
                        }
                    })                
                }
            })                  
        },                
        resetearValores() {          
            this.docente_seleccionado = null                                                     
            this.errors = []               
        },                
    }    
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
    .style-chooser .vs__search::placeholder,
    .style-chooser .vs__dropdown-toggle,
    .style-chooser .vs__dropdown-menu {    
        max-height: 150px;
    }
</style>
