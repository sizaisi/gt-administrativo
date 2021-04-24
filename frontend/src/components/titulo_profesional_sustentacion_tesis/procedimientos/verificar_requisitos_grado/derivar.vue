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
                    <b-tab :title="'1. '+ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1)+' expediente'" 
                        title-item-class="disabledTab" :disabled="tabIndex2 < 0">
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
                    <b-button @click="nextTab" :disabled="tabIndex == 0">Siguiente</b-button>
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
import movimiento_expediente from '../../recursos/movimiento_expediente.vue'

export default {
    name: 'derivar',
    props: {                     
        ruta: Object,
        movimiento: Object
    },
    components: {            
        movimiento_expediente,           
    },
    data() {
        return {             
            url: this.$root.API_URL,                  
            tabIndex: 0,         
            tabIndex2: 0,                                                   
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
                       
            if (pasar) {
                this.tabIndex2++
                this.$nextTick(function () {
                    this.tabIndex++        
                })  
            }              
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