<template>  
  <div id="app">
    <b-navbar toggleable="lg" type="dark" class="nav-administrativo">
      <b-navbar-brand href="#">Grados y Títulos - UNSA</b-navbar-brand>
      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>          
          <template v-for="(menu, index) in array_menu">
            <template v-if="menu.submenu.length == 1">
              <b-nav-item :key="index" :to="{ name: menu.submenu[0].componente }">
                {{ menu.nombre }}
              </b-nav-item>
            </template>                        
            <template v-else-if="menu.submenu.length > 1">
              <b-nav-item-dropdown :key="index" :text="menu.nombre" left>            
                <b-dropdown-item v-for="(submenu, index) in menu.submenu" :key="index" 
                  :to="{ name: submenu.componente }"> {{ submenu.nombre }}
                </b-dropdown-item>                
              </b-nav-item-dropdown>
            </template>                        
          </template>
        </b-navbar-nav>        
      </b-collapse>
    </b-navbar>        
    <router-view/> <!--Mostrar contenido de la página-->
  </div>
</template>
<script>
export default {
    name: 'app', 
    data() {
      return {                               
        url: this.$root.API_URL,            
        codi_usuario: null,
        codi_menu_grup: null,                         
        array_menu: [],
      }
    },
    created() {
      this.getCodiOper()
    },    
    methods: {
      getCodiOper() {       
        this.axios.get(`${this.url}/codi_oper.php`)
        .then(response => {                  
          if (!response.data.error) {
            this.codi_usuario = response.data.codi_oper
            this.codi_menu_grup = response.data.codi_menu_grup                                                                                                                                                                                       
            this.getUsuario()
          }
          else {
            this.$root.mostrarNotificacion('Advertencia!', 'warning', 4000, 'error', response.data.message, 'bottom-right')
          }                  
        })
      },   
      getUsuario() {                       
        let formData = new FormData()
        formData.append('codi_usuario', this.codi_usuario)               

        this.axios.post(`${this.url}/Usuario/getIdUsuario`, formData)
        .then(response => {                
            if (!response.data.error) {
                let usuario = response.data.usuario                     
                this.$store.dispatch('setUsuario', usuario)
                this.getMenus()                
            }
            else {                                        
                console.log(response.data.message)            
            }
        })
      },     
      getMenus() {                                   
        let formData = new FormData()
        formData.append('codi_usuario', this.codi_usuario)        
        formData.append('codi_menu_grup', this.codi_menu_grup)                

        this.axios.post(`${this.url}/Usuario/menus`, formData)
        .then(response => {                
          if (!response.data.error) {
            this.array_menu = response.data.array_menu                      
          }
          else {
            console.log(response.data.message)
          }            
        })            
      },      
    },    
}
</script>
<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  /*text-align: center;*/
  color: #2c3e50;
}
#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}

.nav-administrativo {
  background-color: #990537;
  color: #fff;
}
.nav-docente {
  background-color: #4A9FCE;
  color: #fff;
}

table#tbl-programa-estudios .flip-list-move {
  transition: transform 1s;
}

table#tbl-modalidad-obtencion .flip-list-move {
  transition: transform 1s;
}  

table#tbl-rol-area .flip-list-move {
  transition: transform 1s;
}    

table#tbl-grado-titulo .flip-list-move {
  transition: transform 1s;
}

table#tbl-grado-modalidad .flip-list-move {
  transition: transform 1s;
}

/*table#tbl-procedimientos .flip-list-move {
    transition: transform 1s;
  }*/ 
/*table#tbl-grado-procedimiento .flip-list-move {
    transition: transform 1s;
}
table#tbl-rutas .flip-list-move {
    transition: transform 1s;
}*/ 
.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
}

.counter {
    background-color:#f5f5f5;
    padding: 25px 15px;
    border-radius: 5px;
    min-height: 250px;
}
.count-title {
    font-size: 40px;
    font-weight: normal;
    margin-top: 10px;
    margin-bottom: 0;
    text-align: center;
}
.count-text {
    font-size: 15px;
    font-weight: normal;
    margin-top: 10px;
    margin-bottom: 0;
    text-align: center;
}
.count-text-title {
    /*color: #dc3545;*/
    color: #4ad1e5;
    height: 50px;
}
</style>
