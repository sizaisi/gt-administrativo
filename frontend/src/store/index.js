import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    usuario: null
  },
  mutations: {
    SET_USUARIO(state, usuario) {
      state.usuario = usuario
    }
  },
  actions: {
    setUsuario({commit}, usuario) {
      commit('SET_USUARIO', usuario)
    }
  },
  modules: {
  }
})
