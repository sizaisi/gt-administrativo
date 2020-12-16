<template>
<div>    
    <b-form @submit.prevent="guardarObservaciones" class="mb-3">
        <b-row class="justify-content-lg-center mb-2">
            <b-col col lg="6">
                <b-form-group
                class="mb-0"
                label="Observaciones:"
                label-for="observaciones"                            
                >
                <b-form-textarea                          
                    id="observaciones"
                    v-model="observacion.descripcion"                            
                    placeholder="Ingrese al menos 30 caracteres"  
                    :state="observacion.descripcion.length >= 30"
                    required
                    rows="3"
                ></b-form-textarea>                            
                </b-form-group>
            </b-col>                    
        </b-row>
        <b-row class="justify-content-lg-center">
            <b-col col lg="6" class="text-right">
                <b-button style="height:34px" variant="success" size="sm" type="submit" title="Guardar">
                    Guardar
                </b-button>
            </b-col>                    
        </b-row>  
    </b-form>  
        <b-table                              
            :items="array_observaciones"
            :fields="columnas_observaciones"                              
            striped
            bordered     
            small                                             
            show-empty
            empty-text="No hay observaciones que mostrar."
            primary-key="id"                        
        >  
            <template v-slot:cell(acciones)="data">                                                           
                <b-button variant="warning" size="sm" title="Editar" @click="editarObservaciones(data.item)" class="mr-1">
                    <b-icon icon="pencil-square"></b-icon>
                </b-button>
                <b-button variant="danger" size="sm" @click="eliminarObservaciones(data.item.id)" title="Eliminar">
                    <b-icon icon="trash"></b-icon>
                </b-button>
            </template>                       
        </b-table>
        <div v-if="errors.length" class="alert alert-danger" role="alert">
            <ul><li v-for="(error, i) in errors" :key="i">{{ error }}</li></ul>
        </div>     
</div>    
</template>
<script>
export default {
    name: 'observaciones',
    props: {        
        expediente: Object,
        idgrado_proc: String,    
        idusuario: String,                        
        ruta: Object
    },
    data() {
        return {             
            url: this.$root.API_URL,                              
            columnas_observaciones: [               
                { key: 'descripcion', label: 'Observaciones' },                        
                { key: 'acciones', label: 'Acciones', class: 'text-center' },            
            ],
            observacion : {
              id : null,
              descripcion : ''
            }, 
            array_observaciones : [],
            errors: [],                                 
        }
    },
    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        }
    },
    created() {                                 
        this.getObservaciones()
    },
    methods: {    
        cantidadObservaciones() {
            return this.array_observaciones.length
        },             
        editarObservaciones(data) {          
            this.observacion = Object.assign({}, data)
        },
        guardarObservaciones() {
            if (this.observacion.id == null) {
                this.registrarObservaciones()
            }
            else {
                this.actualizarObservaciones()
            }            
        },         
        getObservaciones() {            
            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)
            formData.append('idusuario', this.idusuario)
            formData.append('idgrado_proc', this.idgrado_proc)            

            this.axios.post(`${this.url}/Observaciones/show`, formData)
                .then(response => {                
                    if (!response.data.error) {                
                        this.array_observaciones = response.data.array_observaciones
                    }
                    else {                
                        console.log(response.data.message)      
                    }
                })    
        },  
        registrarObservaciones() {   
            this.errors = []

            if (this.observacion.descripcion.length < 30) {              
              this.errors.push("Debe ingresar al menos 30 caracteres.")
              return
            }        

            let formData = new FormData()
            formData.append('idexpediente', this.expediente.id)
            formData.append('idgrado_proc', this.idgrado_proc)
            formData.append('idusuario', this.idusuario)
            formData.append('idruta', this.ruta.id)
            formData.append('descripcion', this.observacion.descripcion)            

            this.axios.post(`${this.url}/Observaciones/store`, formData)
              .then(response => {                        
                  this.resetearValores()
                  if (!response.data.error) {                                              
                    this.$root.successAlert(response.data.message)
                    this.getObservaciones()                           
                  }
                  else {
                    this.$root.errorAlert(response.data.message)
                  }                      
            })                        
        },
        actualizarObservaciones() {   
            this.errors = []
            
            if (this.observacion.descripcion.length < 30) {              
              this.errors.push("Debe ingresar al menos 30 caracteres.")
              return
            }       
            
            let formData = new FormData()
            formData.append('id', this.observacion.id)
            formData.append('descripcion', this.observacion.descripcion)

            this.axios.post(`${this.url}/Observaciones/update`, formData)
              .then(response => {    
                  this.resetearValores()                                                                      
                  if (!response.data.error) {                        
                    this.$root.successAlert(response.data.message)
                    this.getObservaciones()                           
                  }
                  else {
                    this.$root.errorAlert(response.data.message)
                  }                      
            })    
        },
        eliminarObservaciones(id) {    
            let formData = new FormData()
            formData.append('id', id)                

            this.$bvModal.msgBoxConfirm(
              '¿Seguro que quieres eliminar estas observaciones?', {
              title: 'Eliminar observaciones',                    
              okVariant: 'danger',
              okTitle: 'Eliminar',
              cancelTitle: 'Cancelar',          
              centered: true
            })
            .then(value => {
                if (value) {        
                    this.axios.post(`${this.url}/Observaciones/delete`, formData)
                    .then(response => {                                       
                        this.resetearValores()
                        if (!response.data.error) {
                            this.$root.successAlert(response.data.message)
                            this.getObservaciones()                            
                        }
                        else {
                            this.$root.errorAlert(response.data.message)
                        }
                    })                
                }
            })                  
        },                        
        resetearValores() {                
            this.observacion.id = null
            this.observacion.descripcion = ''                                                    
        },        
    }    
}
</script>
<style scoped>
    ul {
        margin-bottom: 0px;    
    }         
</style>