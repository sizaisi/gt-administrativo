<template>
<div class="container p-4" style="background-color: #fff;">
    <h5 class="text-center font-weight-bold text-uppercase text-danger" 
        v-text="grado_modalidad.nombre_grado_titulo + ' - ' + grado_modalidad.nombre_modalidad_obtencion + ': Procedimientos'">
    </h5>
    <div class="text-center mt-3">                           
    <b-button :to="{ name: 'inicio', params: { grado_modalidad: grado_modalidad } }" variant="outline-info"> 
        <b-icon icon="arrow-left-short"></b-icon> Atras
    </b-button>
    </div> 
    <div class="row text-center mt-3" v-for="(group, index) in objectGroups" :key="index">
        <div class="col-lg-4" v-for="(grado_procedimiento, i) in array_grado_procedimiento.slice(index * itemsPerRow, (index + 1) * itemsPerRow)" :key="i">           
        <div class="counter">
            <h5 class="count-text-title" v-text="grado_procedimiento.proc_nombre"></h5>               
            <h2 v-if="grado_procedimiento.total_expedientes == 0" class="timer count-title count-number" v-text="grado_procedimiento.total_expedientes"></h2>      
            <h2 v-else class="timer count-title count-number text-danger" v-text="grado_procedimiento.total_expedientes"></h2>
            <p class="count-text">Solicitudes pendientes</p><br>               
            <b-button 
                pill 
                variant="info"                   
                :to="{ name: 'bandeja', 
                        params: {                                                         
                            grado_modalidad: grado_modalidad,
                            grado_procedimiento: grado_procedimiento
                        } 
                }">
                    Ver expedientes
            </b-button>
        </div>
        </div>                            
    </div>      
</div>
</template>
<script>
export default {
    name: 'procedimientos',     
    props: {            
        grado_modalidad: Object                
    },    
    data() {
        return {                               
            url: this.$root.API_URL,
            usuario: this.$store.state.usuario,
            array_grado_procedimiento : [],                         
            itemsPerRow: 3
        }
    },
    computed: {
        objectGroups () {
            return Array.from(Array(Math.ceil(this.array_grado_procedimiento.length / this.itemsPerRow)).keys())
        }
    },
    created() {                           
        if (this.grado_modalidad != null) {
            this.getGradoProcedimientos()
        }
        else {
            this.$router.push({ name: 'home' }); 
        }                
    },
    methods: {                
        getGradoProcedimientos() {                       
            let formData = new FormData()
            formData.append('idgrado_modalidad', this.grado_modalidad.id)
            formData.append('idrol_area', this.usuario.idrol_area)
            formData.append('idusuario', this.usuario.id)
            formData.append('codi_usuario', this.usuario.codi_usuario)
            formData.append('tipo_usuario', this.usuario.tipo)        
            
            this.axios.post(`${this.url}/GradoProcedimiento/gradoProcedimientos`, formData)
            .then(response => {            
                if (!response.data.error) {
                    this.array_grado_procedimiento = response.data.array_grado_procedimiento
                }
                else {                
                    console.log(response.data.message)
                }
            })
        },           
    }    
}
</script>