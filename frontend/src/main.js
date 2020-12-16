import Vue from 'vue'
import App from './App.vue'

import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css'
Vue.use(Vuesax, {
  theme:{
    colors:{
      primary:'#5b3cc4',
      success:'rgb(23, 201, 100)',
      danger:'rgb(242, 19, 93)',
      warning:'rgb(255, 130, 0)',
      dark:'rgb(36, 33, 69)'
    }
  }
})
import 'material-icons/iconfont/material-icons.css';

import router from './router'
import store from './store'
import config from './config'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import { BootstrapVueIcons } from 'bootstrap-vue'
import vSelect from 'vue-select'
import Multiselect from 'vue-multiselect'

Vue.use(BootstrapVueIcons) 
Vue.use(VueAxios, axios)

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.component('v-select', vSelect)
Vue.component('multiselect', Multiselect)

/************************ Configuración de log ***********************/
import VueLogger from 'vuejs-logger';
const isProduction = process.env.NODE_ENV === 'production';
 
const options = {
    isEnabled: true,
    logLevel : isProduction ? 'error' : 'debug',
    stringifyArguments : false,
    showLogLevel : true,
    showMethodName : true,
    separator: '|',
    showConsoleColors: true
};
 
Vue.use(VueLogger, options);
/*********************************************************************/




import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'bootstrap-vue/dist/bootstrap-vue-icons.min.css'
import 'vue-select/dist/vue-select.css';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import './assets/css/main.css';

Vue.config.productionTip = false

new Vue({
  data: {
    API_URL: config.API_URL,
    FILE_URL: config.FILE_URL,    
    color_acciones : config.color_acciones,
    color_estados : config.color_estados,
    estados : config.estados,
  },  
  methods: {             
    successAlert(message) {            
      this.$vs.notify({
        title: 'Éxito!',
        color: 'success',
        time: 4000,
        icon: 'done',
        text: message,
        position: 'bottom-right',
      })     
    },
    errorAlert(message) {      
      this.$vs.notify({
        title: 'Error!',
        color: 'danger',
        time: 4000,
        icon: 'error_outline',
        text: message,
        position: 'bottom-right',
      })
    }
  },       
  router,
  store,    
  render: h => h(App)
}).$mount('#app')