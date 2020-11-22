<template>
    <div>         
        <b-form inline @submit.prevent="registrarJurado">                                                      
            <b-form-select 
                class="mr-3" 
                v-model="tipo" 
                :options="jurado_tipo" 
                id="tipo_jurado"
                required    
            >
            </b-form-select>                                                
            <div class="col-lg-7">
                <v-select   
                    v-model="iddocente"                                                                                                                                              
                    :options="array_docente"                                              
                    :reduce="docente => docente.id" 
                    label="apn"
                    class="style-chooser"
                    placeholder="-- Elija Jurado --"                                                                                                             
                > 
                    <template #search="{attributes, events}">
                        <input
                            class="vs__search"
                            :required="!iddocente"
                            v-bind="attributes"
                            v-on="events"
                        />
                    </template>
                    <template slot="no-options">
                        Opción no encontrada...
                    </template>
                </v-select>
            </div>
            <b-button variant="success" size="sm" type="submit" title="Añadir">
                <b-icon icon="plus-circle"></b-icon>
            </b-button>
        </b-form>           
        <b-table                              
            :items="array_jurado"
            :fields="columnas_jurado"                              
            striped
            bordered  
            small                                                
            show-empty
            empty-text="No hay jurados que mostrar."
            primary-key="id"
        >         
            <template v-slot:cell(eliminar)="data">                                 
                <b-button variant="danger" size="sm" data-toggle="tooltip" data-placement="left" title="Eliminar" @click="eliminarJurado(data.item.id, data.item.tipo)">
                    <b-icon icon="trash"></b-icon>
                </b-button>
            </template>                     
        </b-table>        
        <div v-if="errors.length" class="alert alert-danger" role="alert">
            <ul>
                <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
            </ul>
        </div>                      
    </div>
</template>
<script>
export default {      
  props: {        
    expediente: Object,
    idgrado_proc: String,    
    idusuario: String,                        
    ruta: Object
  },
  data() {
    return {                       
        url: this.$root.API_URL,                      
        fecha_sorteo : null, 
        array_jurado : [],
        array_docente : [],                                       
        columnas_jurado: [               
            { key: 'tipo', label: 'Tipo / Cargo', class: 'text-center' },
            { key: 'apn', label: 'Docente', class: 'text-left' },
            { key: 'eliminar', label: 'Eliminar', class: 'text-center' }
        ],        
        jurado_tipo: [
                { value: null, text: '--Seleccione tipo--', disabled: true },
                { value: 'presidente', text: 'Presidente', disabled: false },
                { value: 'secretario', text: 'Secretario', disabled: false},                  
                { value: 'suplente', text: 'Suplente', disabled: false }               
        ],
        tipo : null,
        iddocente : null,        
        errors: [],        
    }
  },
  created() {            
    this.getCandidatosJurados()  
    this.getJurados()                  
  },
  methods: {   
    cantidadJurados() {
        return this.array_jurado.length
    },         
    getCandidatosJurados() {
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
    getJurados() {         
        let formData = new FormData()
        formData.append('idexpediente', this.expediente.id)   
        formData.append('idgrado_proc', this.idgrado_proc)   
        formData.append('idusuario', this.idusuario)   

        this.axios.post(`${this.url}/Persona/get_jurado`, formData)
            .then(response => {            
                if (!response.data.error) {
                    this.array_jurado = response.data.array_jurado  

                    for (var i in this.array_jurado) {                                            
                        this.deshabilitarTipoJurado(this.array_jurado[i].tipo) 
                    }                                   
                }
                else {
                    console.log(response.data.message)      
                }
            })   
    },        
    registrarJurado() {            
        let formData = new FormData()
        formData.append('idexpediente', this.expediente.id)               
        formData.append('idgrado_proc', this.idgrado_proc)    
        formData.append('idusuario', this.idusuario)    
        formData.append('idruta', this.ruta.id)    
        formData.append('iddocente', this.iddocente)    
        formData.append('tipo', this.tipo)            

        this.axios.post(`${this.url}/Persona/store`, formData)
            .then(response => {
                this.resetearValores()                                   
                if (!response.data.error) {                        
                    this.$root.mostrarNotificacion('Éxito!', 'success', 4000, 'done', response.data.message, 'bottom-right')
                    this.getJurados()
                }
                else {
                    this.$root.mostrarNotificacion('Error!', 'danger', 4000, 'error_outline', response.data.message, 'bottom-right')                                                                                    
                }                      
            })           
    },    
    eliminarJurado(id, tipo) {                
        let formData = new FormData()
        formData.append('id', id)  
        formData.append('idexpediente', this.expediente.id)  
        formData.append('idgrado_proc', this.idgrado_proc)  
        formData.append('tipo', tipo)                  

        this.$bvModal.msgBoxConfirm(
          '¿Esta seguro de eliminar el jurado seleccionado?', {
          title: 'Eliminar jurado',                    
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
                    if (!response.data.error) {
                        this.$root.mostrarNotificacion('Éxito!', 'success', 4000, 'done', response.data.message, 'bottom-right')
                        this.habilitarTipoJurado(tipo)                            
                        this.getJurados()
                    }
                    else {
                        this.$root.mostrarNotificacion('Error!', 'danger', 4000, 'error_outline', response.data.message, 'bottom-right') 
                    }
                })                
            }
        })                  
    },
    habilitarTipoJurado(tipo) {
        for (let i in this.jurado_tipo) {
            if (this.jurado_tipo[i].value == tipo) {
                this.jurado_tipo[i].disabled = false                     
                break
            }
        }                             
    },
    deshabilitarTipoJurado(tipo) {
        for (let i in this.jurado_tipo) {
            if (this.jurado_tipo[i].value == tipo) {
                this.jurado_tipo[i].disabled = true                     
                break
            }
        }                             
    },       
    resetearValores() {                        
        this.iddocente = null          
        this.tipo = null                 
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