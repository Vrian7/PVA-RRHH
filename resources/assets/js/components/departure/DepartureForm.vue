<template>
  <v-dialog persistent v-model="dialog" max-width="900px" @keydown.esc="close">
    <v-tooltip slot="activator" top>
      <v-icon large slot="activator" dark color="primary">add_circle</v-icon>
      <span>Nueva Salida</span>
    </v-tooltip>
    <v-card>
      <v-toolbar dark color="secondary">
        <v-toolbar-title class="white--text">{{ formTitle }}</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-container grid-list-md layout>
          <v-layout wrap>
            <v-flex xs12>
              <v-form ref="form">
                  <fieldset class="field">
                    <legend>OPCIONES:</legend>
                    <v-flex xs12>
                      <v-layout row wrap>
                        <v-flex xs6>
                          <v-radio-group 
                            v-model="departure_type_id"
                            :mandatory="false"
                            @change="getDepartureReason"
                            v-validate="'required'"
                            name="Tipo"
                            :error-messages="errors.collect('Tipo')"
                            row
                            >
                            <span v-for="departureType in departureTypes" :key="departureType.id">
                              <v-radio :label="departureType.name" :value="departureType.id" color="success" v-if="type_departures.includes(departureType.id)"></v-radio>
                            </span>
                          </v-radio-group>
                          <v-checkbox label="A cuenta de vacación" v-model="selectedItem.on_vacation" v-if="departure_type_id==2 && contractType==1"></v-checkbox>
                          <v-autocomplete
                            v-model="selectedItem.departure_reason_id"
                            :items="departureReasons"
                            item-text="name"
                            item-value="id"
                            label="Motivo"
                            :hint="descriptionReason"
                            persistent-hint
                            @change="getDepartureReasonDesc();checkMonthDayYear()"
                            v-validate="'required'"
                            name="Motivo"
                            :error-messages="errors.collect('Motivo')"
                            :disabled="disabledReason">
                          </v-autocomplete>
                          
                        </v-flex>
                        <v-flex xs6>
                          <v-text-field
                            v-model="selectedItem.destiny"
                            label="Lugar de destino"
                            v-validate="'required'"
                            name="Destino"
                            :error-messages="errors.collect('Destino')"
                          ></v-text-field>
                          <v-textarea
                            v-model="selectedItem.description"
                            label="Observaciones"
                            rows="4"
                          ></v-textarea>
                        </v-flex>
                      </v-layout>
                    </v-flex>
                  </fieldset>
                  <fieldset class="field">
                    <legend>HORARIO:</legend>
                    <v-flex xs12>
                      <v-layout row wrap>
                        <v-flex xs3>
                          <v-menu
                            :close-on-content-click="false"
                            v-model="menuDateDeparture"
                            :nudge-right="40"
                            lazy
                            transition="scale-transition"
                            offset-y
                            full-width
                            max-width="290px"
                            min-width="290px"
                          >
                            <v-text-field
                              slot="activator"
                              v-model="dateDeparture"
                              label="Fecha de salida"
                              prepend-icon="event"
                              v-validate="'required'"
                              name="Fecha de salida"
                              :error-messages="errors.collect('Fecha de salida')"
                              autocomplete='cc-exp-month'
                              clearable
                            ></v-text-field>
                            <v-date-picker 
                            v-model="selectedItem.departure_date" 
                            @input="menuDateDeparture = false" 
                            no-title 
                            locale="es-bo" 
                            :min="$moment().subtract(1, 'days').format('YYYY-MM-DD')"
                            :max="$moment().add(2, 'days').format('YYYY-MM-DD')"
                            @change="checkMonthDayYear"
                            ></v-date-picker>
                          </v-menu>
                        </v-flex>
                        <v-flex xs3>
                          <v-text-field
                            v-model="selectedItem.departure_time" 
                            label="Hora de salida"
                            type="time"
                            name="Hora de retorno"
                            v-validate="'required'"
                            :error-messages="errors.collect('Destino')"
                            :min="start_time"
                            :max="end_time"
                            @change="checkMonthDayYear();"
                          ></v-text-field>
                        </v-flex>
                        <v-flex xs3>
                          <v-menu
                            :close-on-content-click="false"
                            v-model="menuDateReturn"
                            :nudge-right="40"
                            lazy
                            transition="scale-transition"
                            offset-y
                            full-width
                            max-width="290px"
                            min-width="290px"
                          >
                            <v-text-field
                              slot="activator"
                              v-model="dateReturn"
                              label="Fecha de retorno"
                              prepend-icon="event"
                              v-validate="'required'"
                              name="Fecha de retorno"
                              :error-messages="errors.collect('Fecha de retorno')"
                              autocomplete='cc-exp-month'
                              clearable
                            ></v-text-field>
                            <v-date-picker 
                            v-model="selectedItem.return_date" 
                            @input="menuDateReturn = false"
                            no-title 
                            locale="es-bo"
                            :min="selectedItem.departure_date"
                            :max="$moment().add(30, 'days').format('YYYY-MM-DD')"
                            @change="checkMonthDayYear"></v-date-picker>
                          </v-menu>
                        </v-flex>
                        <v-flex xs3>
                          <v-text-field
                            v-model="selectedItem.return_time" 
                            label="Hora de retorno"
                            type="time"
                            name="Hora de retorno"
                            v-validate="'required'"
                            :error-messages="errors.collect('Destino')"
                            :min="start_time"
                            :max="end_time"
                            @change="checkMonthDayYear();"
                          ></v-text-field>
                        </v-flex>                        
                      </v-layout>
                    </v-flex>
                  </fieldset>
              </v-form>
              <v-alert
                v-model="alert"
                dismissible
                type="error"
                v-if="errorMessages"
              >
                <p> Alerta: {{ errorMessages }}</p>
              </v-alert>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-text>  
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="error" @click.native="close"><v-icon>close</v-icon> Cancelar</v-btn>
        <v-btn color="success" :disabled="this.errors.any() || !valid" @click="save()"><v-icon>check</v-icon> Guardar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "DepartureForm",
  props: ["item", "bus"],
  data() {
    return {
      dialog: false,
      valid: true,
      selectedIndex: -1,
      selectedCertificate: {
        document_type_id: null,
        data: null,
      },
      selectedItem: {
        id: null,
        contract_id: null,
        departure_reason_id: null,
        certificate_id: null,
        destiny: null,
        description: null,
        departure_date: null,
        return_date: null,
        departure_time: null,
        return_time: null,
        on_vacation: false
      },
      name: null,
      place: null,
      disabledReason: true,
      departureTypes: [],
      departureReasons: [],
      departureReason: null,
      menuDateDeparture: null,
      dateDeparture: null,
      menuDateReturn: null,
      dateReturn: null,    
      menuTimeDeparture: false,
      menuTimeReturn: false,
      alert: true,
      descriptionReason: null,
      departure_type_id: null,
      availableTime: null,
      availableDay: null,
      errorMessages: null,
      contractType: null,
      start_time: null,
      end_time: null,
      type_reasons: null,
      type_departures: [1],
    };
  },
  computed: {
    formTitle() {
      return this.selectedIndex === -1? "Nueva solicitud" : "Editar solicitud";
    }    
  },
  watch: {
    'selectedItem.departure_date'(val) {
      if (this.selectedItem.departure_date) {
        this.dateDeparture = this.$moment(this.selectedItem.departure_date).format("DD/MM/YYYY");
        this.selectedItem.return_date = this.selectedItem.departure_date;
        this.dateReturn = this.$moment(this.selectedItem.return_date).format("DD/MM/YYYY");
      }
    },
    'selectedItem.return_date'(val) {
      if (this.selectedItem.return_date) {
        this.dateReturn = this.$moment(this.selectedItem.return_date).format("DD/MM/YYYY");
      }
    }
  },
  methods: {
    async initialize() {
      try {
        this.getDepartureType();
        this.getUser();
      } catch (e) {
        console.log(e);
      }
    },
    close() {
      this.dialog = false;
      this.$validator.reset();
      this.bus.$emit("closeDialog", this.departure_type_id);
      this.selectedItem = {
        id: null,
        contract_id: this.selectedItem.contract_id,
        departure_reason_id: null,
        certificate_id: null,
        destiny: null,
        description: null,
        departure_date: null,
        return_date: null,
        departure_time: null,
        return_time: null,
        on_vacation: false
      };
      this.errorMessages = null;
      this.dateDeparture = null;
      this.dateReturn = null;
    },
    async getUser() {
      try {
        let res = await axios.get('/contract/last_contract/' + this.$store.getters.currentUser.employee_id);
        this.selectedItem.contract_id = res.data.id;
        this.contractType = res.data.contract_type_id;
        this.start_time = res.data.job_schedules[0].start_hour + ':' + res.data.job_schedules[0].start_minutes;
        this.end_time = res.data.job_schedules[0].end_hour + ':' + res.data.job_schedules[0].end_minutes;
        if (res.data.job_schedules[1]) {
          this.end_time = res.data.job_schedules[1].end_hour + ':' + res.data.job_schedules[1].end_minutes;
        }
        this.type_reasons = res.data.contract_type.departure_reasons;
        if (this.type_reasons.some(e => e.departure_type_id==2)) {
          this.type_departures.push(2);
        }
      } catch(e){
        console.log(e);
      }
    },
    async getDepartureType() {
      try{
        let res = await axios.get('/departure_type');
        this.departureTypes = res.data;
      } catch(e){
        console.log(e);
      }
    },
    async getDepartureReason() {
      try{
        this.departureReasons = this.type_reasons.filter(e => e.departure_type_id == this.departure_type_id);
        this.disabledReason = false;
        let res2 = await axios.get('/departure_type/' + this.departure_type_id);
        this.selectedCertificate.document_type_id = res2.data.document_type_id;
      } catch(e){
        console.log(e);
      }
    },
    async save() {
      try {
        let valid = await this.$validator.validateAll();
        this.checkMonthDayYear();
        if (valid && this.valid) {
          if (this.selectedIndex === -1) {
            this.selectedCertificate.data = this.selectedItem;
            let certificate = await axios.post('/certificate', this.selectedCertificate);
            this.selectedItem.certificate_id = certificate.data.id;
            let departure = await axios.post('/departure', this.selectedItem);
          } else {
            let departure = await axios.patch("/departure/" + this.selectedItem.id, this.selectedItem);
          }
          this.close();
          this.toastr.success(
            `Solicitado correctamente`
          );
        }
      } catch (e) {
        console.log(e);
      }
    },
    async getDepartureReasonDesc() {
      try{
        let res = await axios.get('/departure_reason/' + this.selectedItem.departure_reason_id);
        this.departureReason = res.data;
        this.descriptionReason = res.data.description;
        this.disabledReason = false;
      } catch(e){
        console.log(e);
      }
    },
    async checkMonthDayYear () {
      if (this.dateDeparture && this.dateReturn && this.selectedItem.departure_time && this.selectedItem.return_time && this.selectedItem.departure_reason_id) {
        this.errorMessages = null;
        this.valid = true;
        let departure_used = await axios.get('/departure/get_departures_used/' + this.$store.getters.currentUser.employee_id);
        var rest = 0;
        var rest_hour = 0;
        var rest_day = 0;
        var h1 = this.selectedItem.departure_time.split(":");
        var h2 = this.selectedItem.return_time.split(":");
        if (h1[0] <= 12 && h2[0] >= 14) { 
          var rest_hour1 = (12 * 60 + 0) - (h1[0] * 60 + parseInt(h1[1]));
          var rest_hour2 = (h2[0] * 60 + parseInt(h2[1])) - (14 * 60 + 30);
          rest_hour = rest_hour1 + rest_hour2;
        } else {
          rest_hour = (h2[0] * 60 + parseInt(h2[1])) - (h1[0] * 60 + parseInt(h1[1]));
        } 
        var f1 = this.$moment(this.selectedItem.departure_date);
        var f2 = this.$moment(this.selectedItem.return_date);

        if (this.departure_type_id == 2 && this.selectedItem.departure_reason_id == 16) { 
          // if (this.on_vacation == false) {
            if (f2.diff(f1, "day") == 0) {
               rest_day = rest_hour;
            } else if (f2.diff(f1, "day") == 1) {
              rest_day = rest_hour + 480;
            } else {
              rest_day = rest_hour + 960;
            }
            if (rest_day > departure_used.data.total_minutes_year_rest) {
              this.errorMessages = 'Solo le queda '+ parseInt(departure_used.data.total_minutes_year_rest / 480) + ' Dias y ' + parseInt(departure_used.data.total_minutes_year_rest % 480 / 60) + ' Horas.';
              this.valid = false;
            } 
          // }
        } else if (this.departure_type_id == 1 && this.selectedItem.departure_reason_id == 1) { 
          if (rest_hour > departure_used.data.total_minutes_month_rest || f2.diff(f1, "day") != 0) {
            this.errorMessages = 'Solo le queda '+ parseInt(departure_used.data.total_minutes_month_rest / 60) + ' Horas y ' + parseInt(departure_used.data.total_minutes_month_rest % 60)+ ' Minutos.';
            this.valid = false;
          } 
        } else {
          if (this.departureReason.day != null) {
            if (f2.diff(f1, "day") >= this.departureReason.day) {
              this.errorMessages = 'Solo puede utilizar '+ this.departureReason.day + ' dias.';
              this.valid = false;
            }
          } else if (this.departureReason.hour != null) {
            if (rest_hour > (this.departureReason.hour * 60) || f2.diff(f1, "day") != 0) {              
              this.errorMessages = 'Solo puede utilizar '+ this.departureReason.hour + ' horas.';
              this.valid = false;
            }
          }
        }
      }
    },
  },
  mounted() {
    this.bus.$on("openDialog", item => {
      this.selectedItem = item;
      this.dialog = true;
      this.selectedIndex = item;
      this.departure_type_id = item.departure_reason.departure_type_id;
      this.getDepartureReason();
      this.dateDeparture = this.$moment(item.dateDeparture).format("DD/MM/YYYY");
      this.dateReturn = this.$moment(item.dateDeparture).format("DD/MM/YYYY");
      this.selectedItem.departure_date = item.departure_date;
      this.selectedItem.return_date = item.return_date;
      this.selectedItem.departure_time = this.$moment.utc(item.departure_time, "HH:mm:ss").format("HH:mm");
      this.selectedItem.return_time = this.$moment.utc(item.return_time, "HH:mm:ss").format("HH:mm");      
    });
    this.initialize();
  }
};
</script>
<style type="text/css">
  .field{ 
  border: #EFEFEF 1px solid;
  color: #999999;
  }
</style>