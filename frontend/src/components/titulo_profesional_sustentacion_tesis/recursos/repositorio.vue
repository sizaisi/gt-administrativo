<template>
  <div v-if="movimiento != null">     
    <template v-if="movimiento.etiqueta == 'derivar'">
      <b-row class="justify-content-lg-center">
        <b-col col lg="8">
          <p class="text-justify">
            Expediente <b>derivado</b> por el procedimiento 
            <b>{{ movimiento.procedimiento_origen }}</b> a cargo de
            <b v-if="movimiento.nomb_oper!=null">{{ movimiento.nomb_oper }}</b> 
            <b v-else>{{ movimiento.apn }}</b> 
            <b v-if="movimiento.rol_area_origen!=null"> ({{ movimiento.rol_area_origen }})</b> 
            <b v-else> ({{ movimiento.tipo_rol }})</b> 
            con fecha <b>{{ movimiento.fecha }} hrs.</b>
          </p>                 
        </b-col>
      </b-row>      
      <b-card no-body>
          <b-tabs 
              v-model="tabIndex" 
              card        
              active-nav-item-class="font-weight-bold text-uppercase text-danger"   
              style="min-height: 250px"                        
          >   
              <b-tab title="1. Añadir URL" title-item-class="disabledTab" :disabled="tabIndex2 < 0">                  
                  <b-form inline class="mb-3" @submit.prevent="actualizarURL">                                     
                    <b-input-group prepend="URL: " class="col-lg-8 pl-0">
                      <b-input type="url" v-model="url_tesis" placeholder="Link o enlace de tesis en repositorio digital" required></b-input>
                    </b-input-group>
                    <b-button variant="success" type="submit">Guardar</b-button>
                  </b-form>

                  <div class="row">
                      <div class="col-lg-12">
                          <table class="table table-bordered table-sm" v-if="expediente.url_repo != null">   
                            <thead>
                              <th class="text-center">Código Exp.</th>
                              <th class="text-center">URL</th>                                
                              <th class="text-center">Editar</th>
                            </thead>
                            <tbody>
                              <tr>                              
                                <td class="text-center" v-text="expediente.codigo"></td>
                                <td class="text-center"><a target="_blank" :href="expediente.url_repo" v-text="expediente.url_repo"></a></td>
                                <td class="text-center">
                                  <b-button variant="warning" size="sm" title="Editar" @click="url_tesis=expediente.url_repo">
                                    <b-icon icon="pencil-square"></b-icon>
                                  </b-button>
                                </td>                                                            
                              </tr>                                                
                            </tbody>
                          </table>                                                    
                      </div>                     
                  </div> 
                  <div v-if="errors.length" class="alert alert-danger" role="alert">
                      <ul>
                          <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
                      </ul>
                  </div>           
              </b-tab>
              <b-tab title="2. Generar documento" title-item-class="disabledTab" :disabled="tabIndex2 < 1">
                <b-form @submit.prevent="generarPdf"  ref="frm_datos_pdf" :action="url_pdf" target="_blank" method="post" class="mb-3">
                  <input type="hidden" name="idexpediente" :value="expediente.id">     
                  <input type="hidden" name="titulo_proyecto" :value="expediente.titulo">      
                  <input type="hidden" name="codigo_expediente" :value="expediente.codigo">                                                              
                  <input type="hidden" name="url" :value="expediente.url_repo">                    
                  <input type="hidden" name="apell_nombres" :value="array_graduando[0].apell_nombres">                 
                                                       
                  <div class="row">
                    <div class="mx-auto"> 
                      <b-button type="submit" variant="success">
                          <b-icon icon="file-earmark-text"></b-icon> Acta de confirmación
                      </b-button> 
                    </div>
                  </div>
                </b-form>  
                <div v-if="errors.length" class="alert alert-danger" role="alert">
                  <ul>
                    <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
                  </ul>
                </div>     
              </b-tab>       
              <b-tab title="3. Adjuntar documento" title-item-class="disabledTab" :disabled="tabIndex2 < 2">
                  <b-form @submit.prevent="registrarDocumento" class="mb-3">                  
                      <b-row>    
                          <b-col sm="12" md="8" lg="8">
                              <b-form-file
                                  v-model="file"                                    
                                  placeholder="Seleccione un archivo..."                            
                                  accept=".jpg, .png, .pdf"  
                                  required                            
                              ></b-form-file>           
                          </b-col>
                          <b-col sm="12" md="4" lg="4">
                              <b-button type="submit" variant="success" title="Subir Archivo" :disabled="array_documento.length > 0">
                                  <b-icon icon="upload"></b-icon>
                              </b-button>
                          </b-col>
                      </b-row>            
                  </b-form>               

                  <div class="row">
                      <div class="col-lg-12">
                          <b-table                              
                              :items="array_documento"
                              :fields="columnas_documento"                              
                              striped
                              bordered     
                              small                                             
                              show-empty
                              empty-text="No hay documentos que mostrar."
                              primary-key="id"
                              :busy="estaOcupado"
                          >  
                              <template v-slot:cell(acciones)="data">                                 
                                  <form ref="show_file" :action="url_show_file" target="_blank" method="post">
                                      <input type="hidden" name="file_id" :value="data.item.id">                                            
                                  </form>   
                                  <b-button variant="info" size="sm" title="Descargar" @click="mostrarArchivo" class="mr-1">
                                      <b-icon icon="download"></b-icon>
                                  </b-button>
                                  <b-button @click="eliminarDocumento(data.item.id)" variant="danger" size="sm" title="Eliminar">
                                      <b-icon icon="trash"></b-icon>
                                  </b-button>
                              </template> 
                              <template v-slot:table-busy>
                                  <div class="text-center text-danger my-2">
                                      <b-spinner class="align-middle"></b-spinner>
                                      <strong>Cargando...</strong>
                                  </div>
                              </template>                                                 
                          </b-table>
                      </div>
                  </div> 
                  <div v-if="errors.length" class="alert alert-danger" role="alert">
                      <ul>
                          <li v-for="(error, i) in errors" :key="i">{{ error }}</li>
                      </ul>
                  </div>
              </b-tab>
              <b-tab title="4. Derivar expediente" title-item-class="disabledTab" :disabled="tabIndex2 < 3">
                  <div class="text-center">
                      <template v-for="(ruta, index) in array_ruta">                     
                          <b-button class="m-1" :variant="btn_color[ruta.etiqueta]" @click="mover(ruta)" :key="index">
                              {{ ruta.etiqueta | capitalize }}
                          </b-button>                 
                      </template>                 
                  </div> 
              </b-tab>
          </b-tabs>
      </b-card>    
      <!-- Control buttons-->
      <div class="text-center">
          <b-button-group class="mt-3">
              <b-button class="mr-1" @click="prevTab" :disabled="tabIndex==0">Anterior</b-button>
              <b-button @click="nextTab" :disabled="tabIndex==3">Siguiente</b-button>
          </b-button-group>     
      </div>     
      
    </template>              
  </div>    
</template>

<script>
export default {      
  props: {
    idgrado_modalidad: String,
    idgrado_proc: String,    
    idusuario: String,
    codi_usuario: String,
    idrol_area: String,
    tipo_rol: String,
    tipo_usuario: String,
    expediente: Object,
    array_graduando: Array,
  },
  data() {
    return {             
      movimiento : null,      
      tabIndex: 0,         
      tabIndex2: 0,              
      url: this.$root.API_URL,      
      url_pdf : `${this.$root.API_URL}/pdfs/titulo_profesional_sustentacion_tesis/acta_confirmacion_url_tesis.php`,      
      url_show_file : this.$root.FILE_URL,   
      array_ruta : [],
      btn_color : this.$root.btn_colors,              
      url_tesis : null,       
      array_documento : [],            
      columnas_documento: [               
          { key: 'nombre', label: 'Nombre' },                        
          { key: 'acciones', label: 'Acciones', class: 'text-center' },            
      ],            
      file: null,
      estaOcupado: false,
      errors: [],                                                   
    }
  },
  methods: {    
    getLastMovimiento() {
        let me = this
        var formData = this._toFormData({
            idgradproc_destino: this.idgrado_proc, 
            idexpediente: this.expediente.id         
        })        

        this.axios.post(`${this.url}/Movimiento/getLastMovimientoByProc`, formData)
        .then(function(response) {
          if (!response.data.error) {              
            me.movimiento = response.data.movimiento         
            console.log(me.movimiento)              
          }
          else {              
            console.log(response.data.message)
          }
        })   
    },                       
    prevTab() {
        this.tabIndex2--       
        this.tabIndex--        
    },  
    nextTab() {      
      let pasar = false
      this.errors = []    
                    
      if (this.tabIndex == 0) {
        pasar = this.validarTab1()
      }        
      if (this.tabIndex == 1) {
        pasar = true
      }        
      if (this.tabIndex == 2) {
        pasar = this.validarTab2()
      }        

      if (pasar) {
        this.tabIndex2++
        this.$nextTick(function () {
            this.tabIndex++        
        })  
      }              
    },   
    validarTab1() {        
      if (!this.expediente.url_repo) {
          this.errors.push("Debe añadir la URL de tesis al expediente")
      }     

      if (!this.errors.length) {
          return true
      }      

      return false
    },
    validarTab2() {   
        if (!this.array_documento.length) {
            this.errors.push("La lista de documentos debe contener 1 elemento.")
        }                

        if (!this.errors.length) {
            return true
        }      

        return false
    },
    generarPdf() {                              
        this.$refs.frm_datos_pdf.submit()
    },
    getRutas() { // rutas del procedimiento
        let me = this
        var formData = this._toFormData({
            idgradproc_origen: this.idgrado_proc,            
        })        

        this.axios.post(`${this.url}/Ruta/getRutasByProc`, formData)
        .then(function(response) {
          if (!response.data.error) {              
            me.array_ruta = response.data.array_ruta                      
          }
          else {             
            console.log(response.data.message)                              
          }
        })   
    },                       
    mover(ruta) { // movimiento para derivar el expediente al siguiente procedimiento
      this.$bvModal.msgBoxConfirm(
          '¿Esta seguro de ' + ruta.etiqueta + ' este expediente?', {
          title: ruta.etiqueta.charAt(0).toUpperCase()+ruta.etiqueta.slice(1),                    
          okVariant: this.btn_color[ruta.etiqueta],
          okTitle: 'SI',
          cancelTitle: 'NO',          
          centered: true
        })
          .then(value => {
            if (value) {
              let me = this        
              let formData = this._toFormData({
                    idexpediente: this.expediente.id,
                    idusuario: this.idusuario,
                    idruta: ruta.id,
                    idgradproc_destino: ruta.idgradproc_destino                     
                })                                      

              this.axios.post(`${this.url}/Movimiento/mover`, formData)
              .then(function(response) {                                          
                if (!response.data.error) { //si no hay error                                                       
                  me.$root.$bvToast.toast(response.data.message, {
                    title: 'Éxito!',
                    variant: 'success',
                    toaster: 'b-toaster-bottom-right',                      
                  })
                  me.$router.push({name: 'menu-procedimientos',                   
                                      params: { 
                                        idgrado_modalidad: me.idgrado_modalidad, 
                                        idgrado_proc: me.idgrado_proc, 
                                        idusuario: me.idusuario,
                                        codi_usuario: me.codi_usuario,
                                        idrol_area: me.idrol_area,
                                        tipo_rol: me.tipo_rol,
                                        tipo_usuario: me.tipo_usuario,
                                      }
                                  })                  
                }
                else {                           
                  me.$bvToast.toast(response.data.message, {
                    title: 'Error!',
                    variant: 'danger',
                    toaster: 'b-toaster-bottom-right',                    
                  })                    
                }
              }) 
            }                   
          })              
    },    
    actualizarURL() {                        
        let me = this       
        let formData = this._toFormData({            
          idexpediente: this.expediente.id,
          url_repo: this.url_tesis
        })

        this.axios.post(`${this.url}/Expediente/updateURL`, formData)
            .then(function(response) {                              
                me.url_tesis = null

                if (!response.data.error) {                        
                  me.$bvToast.toast(response.data.message, {
                    title: 'Éxito!',
                    variant: 'success',
                    toaster: 'b-toaster-bottom-right',                      
                  })                    
                  me.getURLExpediente()                                                                
                }
                else {
                  me.$bvToast.toast(response.data.message, {
                    title: 'Error!',
                    variant: 'danger',
                    toaster: 'b-toaster-bottom-right',                      
                  })
                }                      
            })       
            
    },
    getURLExpediente() {
        let me = this      
        let formData = this._toFormData({
            idexpediente: this.expediente.id
        })

        this.axios.post(`${this.url}/Expediente/getURL`, formData)
        .then(function(response) {            
            if (!response.data.error) {
              me.expediente.url_repo = response.data.url_repo
            }
            else {
              console.log(response.data.message)      
            }
        })   
    },   
    getDocumento() { // para mostrar una lista de documentos del procedimiento
        let me = this       
        let formData = this._toFormData({
            idgrado_proc: this.idgrado_proc,
            idusuario: this.idusuario,
            idexpediente: this.expediente.id
        })

        this.axios.post(`${this.url}/Archivo/getDocumento`, formData)
        .then(function(response) {
            if (!response.data.error) {
                me.array_documento = response.data.array_documento                
            }
            else {
                console.log(response.data.message)
            }
        })   
    },       
    registrarDocumento() {         
        let me = this                      
        let formData = this._toFormData({
            nombre: 'Acta de confirmación de URL Tesis',
            data: this.file,
            idgrado_proc: this.idgrado_proc,
            idusuario: this.idusuario,
            idexpediente: this.expediente.id
        })       
        this.estaOcupado = true

        this.axios.post(`${this.url}/Archivo/store`, formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(function(response) {
                me.resetearValores()                                     
                if (response.data.error) {                        
                    me.$bvToast.toast(response.data.message, {
                        title: 'Error!',
                        variant: 'danger',
                        toaster: 'b-toaster-bottom-right',                      
                    })                       
                }
                else {
                    me.$bvToast.toast(response.data.message, {
                        title: 'Éxito!',
                        variant: 'success',
                        toaster: 'b-toaster-bottom-right',                      
                    })                    
                    me.getDocumento()                                                
                }
        })         
    },          
    eliminarDocumento(iddocumento) {
        let me = this               
        let formData = this._toFormData({
            id: iddocumento
        })       

        this.$bvModal.msgBoxConfirm(
          '¿Esta seguro de eliminar el documento?', {
          title: 'Eliminar documento',                    
          okVariant: 'danger',
          okTitle: 'SI',
          cancelTitle: 'NO',          
          centered: true
        })
          .then(value => {
            if (value) {                         
                this.axios.post(`${this.url}/Archivo/delete`, formData)
                .then(function(response) {    
                    me.resetearValores()                       
                    if (response.data.error) {                        
                        me.$bvToast.toast(response.data.message, {
                            title: 'Error!',
                            variant: 'danger',
                            toaster: 'b-toaster-bottom-right',                      
                        })
                    }
                    else {
                        me.$bvToast.toast(response.data.message, {
                            title: 'Éxito!',
                            variant: 'success',
                            toaster: 'b-toaster-bottom-right',                      
                        })                        
                        me.getDocumento()
                    }
                })                
            }
        })                
    },   
    resetearValores() {                             
        this.file = null   
        this.errors = []   
        this.estaOcupado = false                     
    },
    mostrarArchivo() {                           
        this.$refs.show_file.submit()
    },    
    _toFormData(obj) {
        var fd = new FormData()

        for (var i in obj) {
          fd.append(i, obj[i])
        }

        return fd
    },         
  },
  filters: {
    capitalize: function (value) {
        if (!value) return ''
        value = value.toString()
        return value.charAt(0).toUpperCase() + value.slice(1)
    }
  },
  mounted: function() {   
    this.getLastMovimiento()                     
    this.getRutas()          
    this.getDocumento()                 
  },
}
</script>
<style scoped>
    ul {
        margin-bottom: 0px;    
    }         
</style>
<style>
    .disabledTab{
        pointer-events: none;
    }      
</style>