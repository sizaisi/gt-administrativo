<template>
  <div v-if="movimiento != null">
    <div class="row justify-content-center mb-1">
      <fieldset class="col-8 col-md-6 px-3">
        <legend>Elija una opción:</legend>
        <div class="row justify-content-center">      
          <template v-for="(ruta, index) in array_ruta">                             
            <b-form-radio 
              :key="index" 
              v-model="ruta_seleccionada" 
              :value="ruta"
              button
              name="acciones"
              button-variant="outline-primary"
              class="m-2"                      
            >
              {{ ruta.accion }}
            </b-form-radio>            
          </template>                 
        </div>
      </fieldset>
    </div>   

    <component    
      v-if="ruta_seleccionada != null"      
      :is="ruta_seleccionada.componente"
      :ruta="ruta_seleccionada"                            
      :movimiento="movimiento"      
    />             
  </div>    
</template>

<script>
import aprobar_expediente from './aprobar_expediente.vue'

export default {  
  name: 'index',  
  props: {        
    movimiento: Object,
  },
  components: {    
    aprobar_expediente
  },
  data() {
    return {             
      url: this.$root.API_URL,       
      ruta_seleccionada: null,            
    }
  },  
  computed: {
    array_ruta() {
      return this.$store.state.rutas
    }
  },
  created() {                           
    this.$store.dispatch("getRutas");           
  }  
}
</script>
<style scoped>
  fieldset {    
    border-radius: 4px;
    border: 1px solid #ddd;
    height: 100px;
  }

  legend {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;    
    font-size: 14px;
    font-weight: bold;
    padding: 3px 5px 3px 7px;
    width: auto;
  }
</style>