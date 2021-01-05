<template>
    <div class="text-center">                   
        <b-row class="justify-content-lg-center">
        <b-col col lg="8">
            <p class="text-justify">
                <b>Nota: </b> La acción {{ ruta.etiqueta }} permite derivar el expediente al procedimiento 
                <b>{{ ruta.procedimiento_destino }}</b> a cargo de <b>{{ ruta.rol_area_destino }}</b>
            </p>                 
        </b-col>
        </b-row>
        <b-button class="m-1" :variant="color_acciones[ruta.etiqueta]" @click="mover(ruta)">
            {{ ruta.etiqueta | capitalize }}
        </b-button>                                                           
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
            color_acciones : this.$root.color_acciones,
            estados : this.$root.estados
        }
    },
    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        }
    },    
    methods: {                    
        mover(ruta) {
            this.$bvModal.msgBoxConfirm(
                '¿Seguro que quiere ' + ruta.etiqueta + ' este expediente?', {
                title: ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1) + ' Expediente',                    
                okVariant: this.color_acciones[ruta.etiqueta],
                okTitle: ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1),
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
                    formData.append('estado_expediente', this.estados[ruta.etiqueta])                                 

                    console.log(this.expediente)
                    console.log(this.usuario)
                    console.log(this.ruta)
                    console.log(this.movimiento)

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