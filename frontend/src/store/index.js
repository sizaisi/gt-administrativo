import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

import config from './../config'

Vue.use(Vuex)

export default new Vuex.Store({
  strict: process.env.NODE_ENV !== 'production',
  state: {
    usuario: null,    
    grado_modalidad: null,    
    grado_procedimiento: null,    
    expediente:null,
    rutas: [],
    rutaVecinaActiva: false
  },
  mutations: {
    SET_USUARIO(state, usuario) {
      state.usuario = usuario
    },    
    SET_GRADO_MODALIDAD(state, grado_modalidad) {
      state.grado_modalidad = grado_modalidad
    },    
    SET_GRADO_PROCEDIMIENTO(state, grado_procedimiento) {
      state.grado_procedimiento = grado_procedimiento
    },   
    SET_EXPEDIENTE(state, expediente) {
      state.expediente = expediente
    },    
    SET_RUTAS(state, rutas) {
      state.rutas = rutas
    },
    SET_RUTA_ACTIVA(state, rutaVecinaActiva) {
      state.rutaVecinaActiva = rutaVecinaActiva
    }
  },
  actions: {
    setUsuario({commit}, usuario) {
      commit('SET_USUARIO', usuario)
    },
    setGradoModalidad({commit}, grado_modalidad) {
      commit('SET_GRADO_MODALIDAD', grado_modalidad)
    },    
    setGradoProcedimiento({commit}, grado_procedimiento) {
      commit('SET_GRADO_PROCEDIMIENTO', grado_procedimiento)
    },   
    setExpediente({commit}, expediente) {
      commit('SET_EXPEDIENTE', expediente)
    },
    getRutas({commit, state}) {
      let formData = new FormData()
      formData.append('idgradproc_origen', state.grado_procedimiento.id)        

      axios.post(`${config.API_URL}/Ruta/getRutasByProc`, formData)
      .then(response => {                    
        if (!response.data.error) {     
            commit('SET_RUTAS', response.data.array_ruta)                      
        }
        else {              
            console.log(response.data.message)
        }
      })  
    },
    verificarRecursoRutasVecinas({ commit, state }, idruta) {           
      let formData = new FormData()
      formData.append('idexpediente', state.expediente.id)
      formData.append('idgrado_proc', state.grado_procedimiento.id)
      formData.append('idusuario', state.usuario.id)
      formData.append('idruta', idruta)     

      axios.post(`${config.API_URL}/Recurso/verify`, formData)
        .then(response => {                                    
            if (!response.data.error) {     
              commit('SET_RUTA_ACTIVA', response.data.existeRecursoRutaVecinas)                         
            }
            else {                
              console.log(response.data.message)      
            }
        })      
    },
  },
  getters: {
    getUsuario: state => {
      return state.usuario;
    },
    getGradoModalidad: state => {
      return state.grado_modalidad;
    },
    getGradoProcedimiento: state => {
      return state.grado_procedimiento;
    },    
  },
  modules: {
  }

})
