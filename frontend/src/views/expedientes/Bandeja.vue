<template>  
<div class="container-fluid p-4" style="background-color: #fff;">
    <h5 class="text-center font-weight-bold text-uppercase text-danger" v-text="procedimiento.nombre + ': Expedientes'"></h5>      
    <div class="text-center m-3">                           
        <b-button :to="{ name: 'procedimientos' }" variant="outline-info"> 
            <b-icon icon="arrow-left-short"></b-icon> Atras
        </b-button>
    </div> 
    <b-card no-body>
        <b-tabs card active-nav-item-class="font-weight-bold text-uppercase text-danger">
            <b-tab title="Recibidos" @click="getExpedientes(procedimiento.id)" active>                
                <div class="row">
                    <div class="col-lg-12">
                        <b-row>
                            <b-col md="4" class="my-1">
                                <b-form-group label-cols-sm="6" label="Expedientes por página: " class="mb-0">
                                    <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
                                </b-form-group>
                            </b-col>
                            <b-col offset-md="4" md="4" class="my-1">
                                <b-form-group label-cols-sm="3" label="Buscar: " class="mb-0">
                                    <b-input-group>
                                        <b-form-input v-model="filter" placeholder="Escriba el texto a buscar..."></b-form-input>
                                        <b-input-group-append>
                                            <b-button :disabled="!filter" @click="filter = ''">Limpiar</b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                            </b-col>                        
                        </b-row>      
                        <b-table
                            bordered
                            striped
                            :items="array_expediente"
                            :fields="columnas"               
                            show-empty
                            empty-text="No hay expedientes recibidos que mostrar."
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            @filtered="onFiltered" 
                            empty-filtered-text="No hay expedientes que coincidan con su búsqueda."
                            :busy="isBusy"                                
                        >                                                  
                            <template v-slot:cell(acciones)="data">                                 
                                <b-button variant="success" size="sm" data-toggle="tooltip" data-placement="left" title="Evaluar" 
                                :to="{ name: tramite.componente, 
                                        params: { idexpediente: data.item.id } 
                                     }"
                                >
                                <i class="fa fa-edit"></i> Evaluar
                                </b-button>                        
                            </template>
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Cargando...</strong>
                                </div>
                            </template>  
                        </b-table>   
                        <b-row>
                            <b-col offset-md="6" md="6" class="my-1">
                                <b-pagination
                                v-model="currentPage"
                                :total-rows="totalRows"
                                :per-page="perPage"
                                class="my-0"
                                align="right"
                                ></b-pagination>
                            </b-col>
                        </b-row> 
                    </div>
                </div>                                       
            </b-tab>
            <b-tab title="Enviados" @click="getExpedientesEnviados(procedimiento.id)"> 
                <div class="row">
                    <div class="col-lg-12">
                        <b-row>
                            <b-col md="4" class="my-1">
                                <b-form-group label-cols-sm="6" label="Expedientes por página: " class="mb-0">
                                    <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
                                </b-form-group>
                            </b-col>
                            <b-col offset-md="4" md="4" class="my-1">
                                <b-form-group label-cols-sm="3" label="Buscar: " class="mb-0">
                                    <b-input-group>
                                        <b-form-input v-model="filter" placeholder="Escriba el texto a buscar..."></b-form-input>
                                        <b-input-group-append>
                                            <b-button :disabled="!filter" @click="filter = ''">Limpiar</b-button>
                                        </b-input-group-append>
                                    </b-input-group>
                                </b-form-group>
                            </b-col>                        
                        </b-row>      
                        <b-table
                            bordered
                            striped
                            :items="array_exp_enviados"
                            :fields="columnas_enviados"               
                            show-empty
                            empty-text="No hay expedientes enviados que mostrar."
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            @filtered="onFiltered" 
                            empty-filtered-text="No hay expedientes que coincidan con su búsqueda."
                            :busy="isBusy"
                        >                                                 
                            <template v-slot:cell(acciones)="data">                                 
                                <b-button variant="warning" size="sm" title="Deshacer" 
                                    @click="deshacer(data.item.id, data.item.idexpediente, data.item.idproc_origen, data.item.fecha_ant, data.item.etiqueta)">
                                    <i class="fa fa-edit"></i> Deshacer
                                </b-button>                        
                            </template>    
                            <template v-slot:table-busy>
                                <div class="text-center text-danger my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>Cargando...</strong>
                                </div>
                            </template>
                        </b-table>   
                        <b-row>
                            <b-col offset-md="6" md="6" class="my-1">
                                <b-pagination
                                v-model="currentPage"
                                :total-rows="totalRows"
                                :per-page="perPage"
                                class="my-0"
                                align="right"
                                ></b-pagination>
                            </b-col>
                        </b-row> 
                    </div>
                </div>                 
            </b-tab>
        </b-tabs>                                        
    </b-card>
</div>  
</template>
<script>
export default {
  name: 'bandeja',     
  data() {
    return {                               
        url: this.$root.API_URL,
        usuario: this.$store.getters.getUsuario,
        tramite: this.$store.getters.getTramite,
        procedimiento: this.$store.getters.getProcedimiento,
        color_estados : this.$root.color_estados,
        estados : this.$root.estados,                                
        array_expediente : [],  
        array_exp_enviados: [],              
        columnas: [
            { key: 'codigo', label: 'Código', class: 'text-center' },
            { key: 'graduando', label: 'Graduando (s)' },            
            { key: 'escuela', label: 'Escuela' },                    
            { key: 'fecha_recepcion', label: 'Fecha Recepción', class: 'text-center' },            
            { key: 'acciones', label: 'Acciones', class: 'text-center' }
        ],
        columnas_enviados: [
            { key: 'codigo', label: 'Código', class: 'text-center' },
            { key: 'graduando', label: 'Graduando (s)' },            
            { key: 'escuela', label: 'Escuela' },                    
            { key: 'fecha_envio', label: 'Fecha Envio', class: 'text-center' },            
            { key: 'acciones', label: 'Acciones', class: 'text-center' }
        ],    
        totalRows: 1,
        currentPage: 1,
        perPage: 5,
        pageOptions: [5, 10, 15],
        filter: null,  
        isBusy: false,
    }
  },
  created() {         
    if (this.procedimiento != null) {
      this.getExpedientes(this.procedimiento.id)    
    }
    else {
      this.$router.push({ name: 'home' }); 
    }      
  },  
  methods: {            
    getExpedientes(idprocedimiento) {  
        let formData = new FormData()
        formData.append('idprocedimiento', idprocedimiento)
        formData.append('codi_usuario', this.usuario.codi_usuario)                 
        this.toggleBusy()

        this.axios.post(`${this.url}/Expediente/getList`, formData)
            .then(response => {                               
                if (!response.data.error) {
                    this.array_expediente = response.data.array_expediente
                    this.totalRows = this.array_expediente.length;                                 
                }
                else {
                    this.$root.errorAlert(response.data.message)
                }

                this.toggleBusy()
            })                
    },
    getExpedientesEnviados(idprocedimiento) {    
        let formData = new FormData()
        formData.append('idusuario', this.usuario.id)
        formData.append('idproc_origen', idprocedimiento)         
        
        this.toggleBusy()

        this.axios.post(`${this.url}/Movimiento/expedientes_enviados`, formData)
        .then(response => {            
            if (!response.data.error) {
                this.array_exp_enviados = response.data.array_exp_enviados
                this.totalRows = this.array_exp_enviados.length;                     
            }
            else {
               this.$root.errorAlert(response.data.message)
            }
            
            this.toggleBusy()
        })
    },    
    deshacer(idmovimiento, idexpediente, idproc_origen, fecha_ant, etiqueta) {
        this.$bvModal.msgBoxConfirm(
            '¿Seguro que quiere deshacer el movimiento realizado sobre este expediente?', {
            title: 'Deshacer Movimiento',                    
            okVariant: 'success',
            okTitle: 'Deshacer',
            cancelTitle: 'Cancelar',          
            centered: true
        }).then(value => {
            if (value) {
                let formData = new FormData()
                formData.append('id', idmovimiento)
                formData.append('idexpediente', idexpediente)    
                formData.append('idproc_origen', idproc_origen)    
                formData.append('fecha_ant', fecha_ant)    
                formData.append('estado_expediente_ant', this.estados[etiqueta])                                                                 

                this.axios.post(`${this.url}/Movimiento/deshacer`, formData)
                .then(response => {                                                                             
                    if (!response.data.error) {
                        this.$root.successAlert(response.data.message)
                        this.getExpedientesEnviados(idproc_origen)
                    }
                    else {                           
                        this.$root.errorAlert(response.data.message)
                    }
                }) 
            }                   
        })              
    },                    
    toggleBusy() {
        this.isBusy = !this.isBusy
    },         
    onFiltered(filteredItems) {        
        this.totalRows = filteredItems.length
        this.currentPage = 1
    },    
    _countDownChanged(dismissCountDown) {
        this.dismissCountDown = dismissCountDown
    },               
  },  
}
</script>

