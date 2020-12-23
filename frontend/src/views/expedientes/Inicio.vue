<template>
<div class="container p-4" style="background-color: #fff;">
    <div class="row">         
        <div class="col text-center">                     
        <h5 class="font-weight-bold text-uppercase text-danger" v-if="array_grado_modalidad.length > 0" v-text="'Grados - Modalidades'"></h5>		
        <h5 class="text-info" v-else v-text="'No existen solicitudes pendientes'"></h5>		                
        </div>          		
    </div>
    <div class="row text-center mt-3" v-for="(group, index) in objectGroups" :key="index">
        <div class="col-lg-4" v-for="(grado_modalidad, i) in array_grado_modalidad.slice(index * itemsPerRow, (index + 1) * itemsPerRow)" :key="i">           
        <div class="counter">
            <h5 class="count-text-title" v-text="grado_modalidad.nombre_grado_titulo+' - '+grado_modalidad.nombre_modalidad_obtencion"></h5>               
            <h2 class="timer count-title count-number text-danger" v-text="grado_modalidad.total_expedientes"></h2>      
            <p class="count-text">Solicitudes pendientes</p><br>               
            <b-button pill variant="info" @click="mostrarProcedimientos(grado_modalidad)">
                Ver procedimientos
            </b-button>
        </div>
        </div>                            
    </div>      
</div>
</template>
<script>
export default {
    name: 'inicio',         
    data() {
        return {                               
            url: this.$root.API_URL,                        
            array_grado_modalidad : [],                         
            itemsPerRow: 3
        }
    },
    computed: {
        objectGroups () {
            return Array.from(Array(Math.ceil(this.array_grado_modalidad.length / this.itemsPerRow)).keys())
        }
    },
    created() {                           
        this.getGradoModalidad()            
    },
    methods: {                        
        getGradoModalidad() {             
            let usuario = this.$store.getters.getUsuario
            let formData = new FormData()

            formData.append('codi_usuario', usuario.codi_usuario)
            formData.append('idrol_area', usuario.idrol_area)             

            this.axios.post(`${this.url}/GradoModalidad/inicio`, formData)
            .then(response => {                                      
                if (!response.data.error) {
                    this.array_grado_modalidad = response.data.array_grado_modalidad                                                                       
                }
                else {
                    console.log(response.data.message)
                }
            })
        },  
        mostrarProcedimientos(grado_modalidad) {
            this.$store.dispatch('setGradoModalidad', grado_modalidad)   
            this.$router.push( { name: "procedimientos" } );
        }
    },    
}
</script>