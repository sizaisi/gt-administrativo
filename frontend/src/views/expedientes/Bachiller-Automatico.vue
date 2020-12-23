<template>
  <div>
    <div class="container-fluid" style="background-color: #fff; padding:20px;">
      <h5 class="text-center font-weight-bold text-uppercase text-danger" v-text="grado_procedimiento.proc_nombre"></h5>
      <p class="narrow text-center" v-text="grado_procedimiento.proc_descripcion"></p>
      <div class="text-center m-3">
        <b-button :to="{ name: 'bandeja' }" variant="outline-info">
          <b-icon icon="arrow-left-short"></b-icon>Atras
        </b-button>
      </div>
      <b-card no-body>
        <b-tabs vertical card pills>
          <b-tab title="Trámite" active>
            <b-card no-body>
              <b-tabs card justified active-nav-item-class="font-weight-bold text-uppercase text-danger">
                <b-tab title="Graduando">                                    
                  <b-card>                    
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label class="text-info">CUI</label>
                        <label class="lbl-data" v-text="graduando.cui"></label>
                      </div>
                      <div class="form-group col-md-9">
                        <label class="text-info">Apellidos y Nombres</label>
                        <label class="lbl-data" v-text="graduando.apell_nombres"></label>
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label class="text-info">E-mail</label>
                        <label class="lbl-data" v-text="graduando.email"></label>
                      </div>
                      <div class="form-group col-md-3">
                        <label class="text-info">Teléfono</label>
                        <label class="lbl-data" v-text="graduando.telefono_movil"></label>
                      </div>
                      <div class="form-group col-md-6">
                        <label class="text-info">Dirección</label>
                        <label class="lbl-data" v-text="graduando.direccion"></label>
                      </div>
                    </div>                    
                  </b-card>
                </b-tab>
                <b-tab title="Expediente">                               
                  <b-card>                    
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label class="text-info">Código</label>
                        <label class="lbl-data" v-text="expediente.codigo"></label>
                      </div>                      
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label class="text-info">Fecha de creación</label>
                        <label class="lbl-data" v-text="expediente.fing"></label>
                      </div>
                      <div class="form-group col-md-8">
                        <label class="text-info">Programa de estudios</label>
                        <label class="lbl-data" v-text="expediente.escuela"></label>
                      </div>                                                                        
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-4">
                        <label class="text-info">Fecha primera matrícula</label>
                        <label class="lbl-data" v-text="expediente.fecha_primera_matricula"></label>
                      </div>                        
                      <div class="form-group col-md-4">
                        <label class="text-info">Fecha máxima de evaluación</label>
                        <label class="lbl-data" v-text="expediente.max_fecha_evaluacion"></label>
                      </div>                        
                      <div class="form-group col-md-4">
                        <label class="text-info">Créditos</label>
                        <label class="lbl-data" v-text="expediente.creditos"></label>
                      </div>                                                                        
                    </div>                    
                  </b-card>
                </b-tab>                
                <b-tab title="Documentos">                                    
                  <form ref="show_file" :action="url_show_file" target="my-frame" method="post">
                    <input type="hidden" name="file_id" />
                  </form>
                  <b-table
                    :items="array_archivo"
                    :fields="columnas_archivos"
                    striped
                    bordered
                    hover
                    small
                    responsive
                    show-empty
                    empty-text="No hay documentos que mostrar."
                    primary-key="id"
                  >
                    <template v-slot:cell(descargar)="data">
                      <b-button
                        variant="warning"
                        size="sm"
                        title="Descargar"
                        @click="mostrarArchivo(data.item.id, data.item.nombre)"
                      >
                        <b-icon icon="eye"></b-icon>
                      </b-button>
                    </template>
                  </b-table>
                </b-tab>
              </b-tabs>
            </b-card>
          </b-tab>
          <b-tab title="Procedencia">
            <b-card no-body>
              <b-tabs card active-nav-item-class="font-weight-bold text-uppercase text-danger">
                <b-tab title="Estado de expediente">                 
                  <b-row class="justify-content-lg-center">
                    <b-col col lg="10">
                      <table class="table table-bordered table-borderless">
                        <tbody>
                          <tr>
                            <th class="bg-light text-right">Procedimiento:</th>
                            <td v-text="movimiento.procedimiento_origen"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-right">Rol / Área:</th>
                            <td
                              v-if="movimiento.rol_area_origen != null"
                              v-text="movimiento.rol_area_origen"
                            ></td>
                            <td v-else v-text="movimiento.tipo_rol"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-right">Responsable:</th>
                            <td v-text="movimiento.nombre_usuario"></td>
                          </tr>
                          <tr>
                            <th class="bg-light text-right">Estado:</th>
                            <td>
                              <b-badge
                                :variant="color_acciones[movimiento.etiqueta]"
                                v-text="estados[movimiento.etiqueta]"
                                class="estado"
                              ></b-badge>
                            </td>
                          </tr>
                          <tr>
                            <th class="bg-light text-right">Fecha / Hora:</th>
                            <td v-text="movimiento.fecha + ' hrs.'">johncarter@mail.com</td>
                          </tr>
                        </tbody>
                      </table>
                    </b-col>
                  </b-row>
                </b-tab>
                <b-tab title="Archivos adjuntos" v-if="array_archivo_ultimo.length > 0">                                    
                  <form
                    ref="show_file_ultimo"
                    :action="url_show_file"
                    target="my-frame"
                    method="post"
                  >
                    <input type="hidden" name="file_id" />
                  </form>
                  <b-table
                    :items="array_archivo_ultimo"
                    :fields="columnas_archivos_ultimo"
                    striped
                    bordered
                    borderless
                    responsive
                    show-empty
                    empty-text="No hay archivos que mostrar."
                    primary-key="id"
                  >
                    <template v-slot:cell(descargar)="data">
                      <b-button
                        variant="warning"
                        size="sm"
                        title="Descargar"
                        @click="mostrarArchivoUltimo(data.item.id, data.item.nombre)"
                      >
                        <b-icon icon="eye"></b-icon>
                      </b-button>
                    </template>
                  </b-table>
                </b-tab>
                <!--<b-tab title="Observaciones">
                    //Información observaciones procedimiento procedencia
                    <div class="mb-4">
                      <h4 class="text-info text-center">Observaciones</h4>
                    </div>                               
                </b-tab>-->
              </b-tabs>
            </b-card>
          </b-tab>
          <b-tab title="Derivación" v-if="movimiento != null">            
            <component
              :is="nombre_componente"
              :grado_modalidad="grado_modalidad"
              :grado_procedimiento="grado_procedimiento"
              :usuario="usuario"
              :expediente="expediente"
              :graduando="graduando"
              :movimiento="movimiento"
              v-if="graduando != null"
            />
          </b-tab>
        </b-tabs>
      </b-card>
    </div>

    <!--Inicio del modal-->
    <div
      class="modal fade centered-modal"
      tabindex="-1"
      :class="{'mostrar' : modal}"
      role="dialog"
      aria-labelledby="myModalLabel"
      style="display: none;"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-primary modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="nombre_documento"></h4>
            <button type="button" class="close" aria-label="Close" @click="modal = 0">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <iframe
              name="my-frame"
              :src="url_show_file"
              width="660"
              height="380"
              frameborder="0"
              allowtransparency="true"
            ></iframe>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="modal = 0">Cerrar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
  </div>
</template>
<script>
import verificar_pagos_adjuntar_documentos from "@/components/bachiller_automatico/procedimientos/verificar_pagos_adjuntar_documentos/index.vue";
import aprobar_consejo_facultad_autorizar_emision_diploma from "@/components/bachiller_automatico/procedimientos/aprobar_consejo_facultad_autorizar_emision_diploma/index.vue";
import generar_imprimir_diploma from "@/components/bachiller_automatico/procedimientos/generar_imprimir_diploma/index.vue";

export default {
  name: "info-expediente",
  props: {
    nombre_componente: String,  
    idexpediente: String,
  },
  components: {
    verificar_pagos_adjuntar_documentos,
    aprobar_consejo_facultad_autorizar_emision_diploma,
    generar_imprimir_diploma,
  },
  data() {
    return {
      url: this.$root.API_URL,
      url_show_file: `${this.$root.API_URL}/utils/show_file.php`,
      usuario: this.$store.getters.getUsuario,
      grado_modalidad: this.$store.getters.getGradoModalidad,
      grado_procedimiento: this.$store.getters.getGradoProcedimiento,
      expediente: {},
      graduando: {},
      movimiento: {},
      estados: this.$root.estados,
      color_acciones: this.$root.color_acciones,
      array_archivo: [], 
      array_archivo_ultimo: [],
      columnas_archivos: [
        { key: "nombre", label: "Nombre", class: "text-left", sortable: true },
        {
          key: "procedimiento",
          label: "Procedimiento",
          class: "text-left",
          sortable: true,
        },
        {
          key: "area",
          label: "Rol-Area",
          class: "text-center",
          sortable: true,
        },
        { key: "descargar", label: "Archivo", class: "text-center" },
      ],
      columnas_archivos_ultimo: [
        { key: "nombre", label: "Nombre", class: "text-left" },
        { key: "procedimiento", label: "Procedimiento", class: "text-left" },
        { key: "area", label: "Rol-Area", class: "text-center" },
        { key: "descargar", label: "Archivo", class: "text-center" },
      ],
      modal: 0,
      nombre_documento: "",
    };
  },
  created() {
    if (this.idexpediente != null) {
      this.getLastMovimiento();
      this.getExpediente();
      this.getGraduando();
      this.getArchivos();
      this.getArchivosProcOrigen();
    } else {
      this.$router.push({ name: "home" });
    }
  },
  methods: {
    getLastMovimiento() {            
      let formData = new FormData()
      formData.append('idgradproc_destino', this.grado_procedimiento.id)
      formData.append('idexpediente', this.idexpediente)        

      this.axios
        .post(`${this.url}/Movimiento/ultimoMovimiento`, formData)
        .then(response => {          
          if (!response.data.error) {
            this.movimiento = response.data.movimiento;
          } else {
            console.log(response.data.message)
          }
        });
    },
    mostrarArchivo(id, nombre_documento) {
      this.$refs.show_file.file_id.value = id;
      this.$refs.show_file.submit();
      this.nombre_documento = nombre_documento;
      this.modal = 1;
    },
    mostrarArchivoUltimo(id, nombre_documento) {
      this.$refs.show_file_ultimo.file_id.value = id;
      this.$refs.show_file_ultimo.submit();
      this.nombre_documento = nombre_documento;
      this.modal = 1;
    },
    getExpediente() {     
      let formData = new FormData()
      formData.append('idexpediente', this.idexpediente)      

      this.axios
        .post(`${this.url}/Expediente/getExpById`, formData)
        .then(response => {          
          if (!response.data.error) {
            this.expediente = response.data.expediente;   
            this.$store.dispatch('setExpediente', this.expediente)         
          } else {
            console.log(response.data.message)
          }
        });
    },
    getGraduando() {            
      let formData = new FormData()
      formData.append('idexpediente', this.idexpediente)

      this.axios
        .post(`${this.url}/GraduandoExpediente/getGraduando`, formData)
        .then(response => {                    
          if (!response.data.error) {
            this.graduando = response.data.graduando;
          } else {
            console.log(response.data.message)
          }
        });
    },
    getArchivos() {      
      let formData = new FormData()
      formData.append('idexpediente', this.idexpediente)

      this.axios
        .post(`${this.url}/Archivo/index`, formData)
        .then(response => {          
          if (!response.data.error) {
            this.array_archivo = response.data.array_archivo;
          } else {
            console.log(response.data.message)
          }
        });
    },
    getArchivosProcOrigen() {            
      let formData = new FormData()
      formData.append('idgrado_proc', this.grado_procedimiento.id)
      formData.append('idexpediente', this.idexpediente)  

      this.axios
        .post(`${this.url}/Archivo/show`, formData)
        .then(response => {          
          if (!response.data.error) {
            this.array_archivo_ultimo = response.data.array_archivo_ultimo;
          } else {
            console.log(response.data.message)
          }
        });
    },    
  },  
};
</script>
<style scoped>
.lbl-data {
  border: 0;
  padding: 0;
  font-weight: bold;
  display: block;
  width: 100%;
  font-size: 1rem;
  margin-bottom: 0;
}
.estado {
  font-size: 0.8em;
}
/* Modal styles */
.modal .modal-dialog {
  max-width: 700px;
  margin: 3.75rem auto;
}
.modal .modal-header,
.modal .modal-body,
.modal .modal-footer {
  padding: 10px 20px;
  border-radius: 5px;
}
.modal .modal-content {
  border-radius: 5px;
}
.modal .modal-footer {
  background: #ecf0f1;
  border-radius: 5px;
}
.modal .modal-title {
  display: inline-block;
}
.modal form label {
  font-weight: 600;
}
.modal-content {
  width: 100% !important;
  position: absolute !important;
  left: 0%;
  margin-top: 20px !important;
}
.mostrar {
  display: list-item !important;
  opacity: 1 !important;
  position: fixed !important;
  background: rgba(0, 0, 0, 0.6) !important;
}
</style>

