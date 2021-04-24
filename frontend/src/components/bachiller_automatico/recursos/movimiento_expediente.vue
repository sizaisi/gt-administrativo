<template>
    <div class="text-center">                   
        <b-row class="justify-content-lg-center">
        <b-col col lg="8">
            <p class="text-justify">
                <b>Nota: </b> La acción {{ ruta.accion }} permite derivar el expediente al procedimiento 
                <b>{{ ruta.procedimiento_destino }}</b> a cargo de <b>{{ ruta.rol_area_destino }}</b>
            </p>                 
        </b-col>
        </b-row>
        <b-button class="m-1" variant="success" @click="mover(ruta)">Derivar</b-button>
    </div>            
</template>
<script>

export default {
    name: 'movimiento-expediente',
    props: {                        
        movimiento: Object, //ultimo movimiento      
        ruta: Object            
    },    
    data() {
        return {             
            url: this.$root.API_URL,     
            usuario: this.$store.getters.getUsuario,                                     
            expediente: this.$store.getters.getExpediente,                          
        }
    },    
    methods: {                    
        mover(ruta) {
            this.$bvModal.msgBoxConfirm(
                '¿Seguro que quiere derivar este expediente?', {
                title: 'Derivar Expediente',                    
                okVariant: 'success',
                okTitle: 'Derivar',
                cancelTitle: 'Cancelar',          
                centered: true
            }).then(value => {
                if (value) {
                    let formData = new FormData()
                    formData.append('idexpediente', this.expediente.id)  
                    formData.append('idusuario', this.usuario.id)
                    formData.append('idruta', ruta.id)
                    formData.append('idproc_origen', ruta.idproc_origen)
                    formData.append('idproc_destino', ruta.idproc_destino)
                    formData.append('idmov_anterior', this.movimiento.id)
                    formData.append('estado_expediente', ruta.accion)                                 

                    this.axios.post(`${this.url}/Movimiento/mover`, formData)
                        .then(response => {                                                                  
                            if (!response.data.error) {
                                this.$root.successAlert(response.data.message)
                                this.$router.push({name: 'bandeja'})                  
                            }
                            else {                           
                                this.$root.errorAlert(response.data.message)
                            }
                        }) 
                }                   
            })              
        },                             
    }    
}
</script>