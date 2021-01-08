<template>
<div class="container p-4" style="background-color: #fff;">
    <h5 class="text-center font-weight-bold text-uppercase text-danger" 
        v-text="grado_modalidad.grado_titulo + ' - ' + grado_modalidad.modalidad + ': Procedimientos'">
    </h5>
    <div class="text-center mt-3">                           
    <b-button :to="{ name: 'inicio' }" variant="outline-info"> 
        <b-icon icon="arrow-left-short"></b-icon> Atras
    </b-button>
    </div> 
    <div class="row text-center mt-3" v-for="(group, index) in objectGroups" :key="index">
        <div class="col-lg-4" v-for="(procedimiento, i) in array_procedimiento.slice(index * itemsPerRow, (index + 1) * itemsPerRow)" :key="i">           
        <div class="counter">
            <h5 class="count-text-title" v-text="procedimiento.nombre"></h5>               
            <h2 v-if="procedimiento.total_expedientes == 0" class="timer count-title count-number" v-text="procedimiento.total_expedientes"></h2>      
            <h2 v-else class="timer count-title count-number text-danger" v-text="procedimiento.total_expedientes"></h2>
            <p class="count-text">Solicitudes pendientes</p><br>               
            <b-button pill variant="info" @click="mostrarBandeja(procedimiento)">
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
    data() {
        return {                               
            url: this.$root.API_URL,            
            grado_modalidad: this.$store.getters.getGradoModalidad,
            array_procedimiento : [],                         
            itemsPerRow: 3
        }
    },
    computed: {
        objectGroups () {
            return Array.from(Array(Math.ceil(this.array_procedimiento.length / this.itemsPerRow)).keys())
        },        
    },
    created() {                           
        if (this.grado_modalidad != null) {
            this.getProcedimientos()
        }
        else {
            this.$router.push({ name: 'home' }); 
        }                
    },
    methods: {                
        getProcedimientos() {        
            let usuario = this.$store.getters.getUsuario               
            let formData = new FormData()
            
            formData.append('idgrado_modalidad', this.grado_modalidad.id)
            formData.append('idrol', usuario.idrol)            
            formData.append('codi_usuario', usuario.codi_usuario)               
            
            this.axios.post(`${this.url}/Procedimiento/getProcedimientos`, formData)
            .then(response => {                                             
                if (!response.data.error) {
                    this.array_procedimiento = response.data.array_procedimiento
                }
                else {                
                    console.log(response.data.message)
                }
            })
        },   
        mostrarBandeja(procedimiento) {
            this.$store.dispatch('setProcedimiento', procedimiento)   
            this.$router.push( { name: "bandeja" } );
        }        
    }    
}
</script>
