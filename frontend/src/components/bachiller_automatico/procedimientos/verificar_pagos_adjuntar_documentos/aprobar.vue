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
                    <b-tab title="1. Verificar pagos" title-item-class="disabledTab" :disabled="tabIndex2 < 0">
                        <b-table
                            bordered
                            striped
                            :items="array_pagos"
                            :fields="columnas_pagos"               
                            show-empty
                            empty-text="No hay pagos que mostrar."                     
                            :busy="isBusy"                                
                        >                        
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Cargando...</strong>
                                </div>
                            </template>  
                        </b-table>
                        <div v-if="errors.length" class="alert alert-danger" role="alert">
                            <ul>
                                <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
                            </ul>
                        </div>      
                    </b-tab>                      
                    <b-tab title="2. Adjuntar documentos" title-item-class="disabledTab" :disabled="tabIndex2 < 1">                        
                        <documentos                                                                                                                                     
                            :ruta="ruta"                                                           
                            ref="documentos"
                            max_docs="2"
                            :array_opciones="array_tipo_documento"
                        />                        
                        <div v-if="errors.length" class="alert alert-danger" role="alert">
                            <ul><li v-for="(error, i) in errors" :key="i">{{ error }}</li></ul>
                        </div>       
                    </b-tab>                   
                    <b-tab :title="'3. '+ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1)+' expediente'" 
                        title-item-class="disabledTab" :disabled="tabIndex2 < 2">
                        <movimiento_expediente                                                                                                          
                            :movimiento="movimiento"
                            :ruta="ruta"                                                            
                        />
                    </b-tab>
                </b-tabs>
            </b-card>        
            <div class="text-center">
                <b-button-group class="mt-3">
                    <b-button class="mr-1" @click="prevTab" :disabled="tabIndex == 0">Anterior</b-button>
                    <b-button @click="nextTab" :disabled="tabIndex == 2">Siguiente</b-button>
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

import documentos from '../../recursos/documentos.vue'
import movimiento_expediente from '../../recursos/movimiento_expediente.vue'

export default {
    name: 'aprobar',
    props: {                             
        ruta: Object,
        movimiento: Object
    },
    components: {                    
        documentos,
        movimiento_expediente,           
    },
    data() {
        return {             
            url: this.$root.API_URL,   
            graduando: this.$store.getters.getGraduando,
            expediente: this.$store.getters.getExpediente,                             
            tabIndex: 0,         
            tabIndex2: 0, 
            array_conceptos : [13, 27, 961],                
            conceptos : {
                13: 'CONSTANCIA DE INGRESO O DE EGRESADO',
                27: 'CONSTANCIA DE BIBLIOTECA',
                961: 'CONSTANCIA PRIMERA MATRICULA PREGRADO'
            },
            array_pagos : [],                        
            array_tipo_documento: [
                { value: null, text: '--Seleccione tipo--', disabled: true },                
                { value: 'Constancia de idiomas', text: 'Constancia de idiomas', disabled: false}, 
                { value: 'Antecedentes penales', text: 'Antecedentes penales', disabled: false }                
            ],            
            columnas_pagos: [
                { key: 'concepto', label: 'Concepto de pago', class: 'text-left' },
                { key: 'nro_recibo', label: 'Núm. recibo', class: 'text-center' },            
                { key: 'fecha_pago', label: 'Fecha de pago', class: 'text-center' },                    
                { key: 'monto', label: 'Monto pagado', class: 'text-center' }                
            ],
            asesor : null,  //object                                         
            errors: [], 
            isBusy: false,
        }
    },
    computed: {
        existeRecursoRutaVecinas() {
            return this.$store.state.rutaVecinaActiva
        }
    },
    created() {                          
        this.$store.dispatch("verificarRecursoRutasVecinas", this.ruta.id);           
        this.getPagos()       
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
                pasar = this.validarTab2()
            }                   
            
            if (pasar) {
                this.tabIndex2++
                this.$nextTick(function () {
                    this.tabIndex++        
                })  
            }              
        },   
        validarTab1() {                   
            this.verificarPagos()

            if (!this.errors.length) {
                return true
            }      

            return false
        },
        validarTab2() {        
            if (this.$refs.documentos.cantidadDocumentos() < 2) { //referencia al metodo del componente hijo
                this.errors.push("Debe adjuntar 2 documentos para el expediente seleccionado.")
            }                        

            if (!this.errors.length) {
                return true
            }      

            return false
        },        
        getPagos() {     
            let formData = new FormData()
            formData.append('cui', this.graduando.cui)                   
            formData.append('nues', this.expediente.nues)                   
            formData.append('espe', this.expediente.espe)                              
            this.toggleBusy()

            this.axios.post(`${this.url}/Caja/getPagosProfesionalTesis`, formData)
                .then(response => {    
                    if (!response.data.error) {                
                        this.array_pagos = response.data.array_pagos                        
                    }
                    else {                
                        console.log(response.data.message)      
                    }
                    this.toggleBusy()
                })
        },   
        verificarPagos() {              
            if (this.array_pagos.length > 0) {              
                for (let i = 0; i < this.array_conceptos.length; i++) {
                    let encontrado = false

                    for (let j = 0; j < this.array_pagos.length; j++) {
                        if (this.array_pagos[j].idconcepto == this.array_conceptos[i]) {
                            encontrado = true
                            break
                        }
                    }

                    if (encontrado == false) {
                        this.errors.push(`No se encontró el concepto: ${this.conceptos[this.array_conceptos[i]]}`)
                    }
                }            
            }
        },                 
        toggleBusy() {
            this.isBusy = !this.isBusy
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
</style>