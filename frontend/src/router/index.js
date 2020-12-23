import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'home',
    component: require("./../views/Home.vue").default,  
  },
  {
    path: '/expedientes/inicio',
    name: 'inicio',
    component: require("./../views/expedientes/Inicio.vue").default,      
  },
  {
    path: '/expedientes/procedimientos',
    name: 'procedimientos',
    component: require("./../views/expedientes/Procedimientos.vue").default,       
  },
  {
    path: '/expedientes/bandeja',
    name: 'bandeja',
    component: require("./../views/expedientes/Bandeja.vue").default,       
  },
  {
    path: '/expedientes/info-expediente2',
    name: 'info-expediente2', //2=>id grado modalidad (bachiller - automatico)
    component: require("./../views/expedientes/Bachiller-Automatico.vue").default, 
    props: true
  },
  {
    path: '/expedientes/info-expediente1',
    name: 'info-expediente1', //1=>id grado modalidad (titulo profesional - sustentacion tesis)
    component: require("./../views/expedientes/TituloProfesional-SustentacionTesis.vue").default, 
    props: true
  },
  {
    path: '/reportes',
    name: 'reportes',
    component: require("./../views/Home.vue").default,  
    props: true  
  },  
  {
    path: '/configuracion',
    name: 'configuracion',
    component: require("./../views/Home.vue").default,  
    props: true  
  },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,  
  routes
})

export default router
