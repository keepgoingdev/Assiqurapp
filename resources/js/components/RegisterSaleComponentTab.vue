<template>
  <form-wizard ref="formWizard" @on-complete="onComplete" shape = "circle" color="#e67e22" title = "" subtitle = "">
            <tab-content title="l'età " icon="ti-user" :before-change="ageStepValidate">
                <div class="container justify-content-center align-items-center align-content-center align-self-center" id="app-head" style="background-image:url(&quot;assets/img/team-motivation-teamwork-together-53958.jpeg&quot;);background-position:center;background-size:cover;background-repeat:no-repeat;">
                    <h4 class="text-center text-head" style="font-family:Montserrat, sans-serif;font-style:normal;font-weight:bold;font-size:15px;padding-top:21px;color:rgb(230,230,230);">Ognuno di noi ha tante cose a cui tiene,<br></h4>
                    <div class="row text-center">
                        <img src="assets/img/LogoneBlalloNice.png" id="center-img-head" style="width:50px;margin:0 auto;">
                    </div>
                    <h4 class="text-center text-head" style="font-weight:bold;font-family:Montserrat, sans-serif;font-size:16px;color:rgb(255,255,255);padding-top:-36px;">proteggile tutte con un unica soluzione.<br></h4>
                </div>
                <div class="container">
                    <p class="para-data" style="color:rgba(33,37,41,0.84);">Scegli il prodotto più completo che garantisca Tutela e Risparmio.&nbsp;</p>
                    <p class="para-data data-calco">Inserisci letà e ottieni la tua soluzione</p>
                    <div class = "age-wrapper">
                        <NumberInputSpinner :min="18" :max="59" v-model="form.age" />
                    </div>
                </div>
            </tab-content>
            <tab-content title="protezioni" icon="ti-hand-point-right">
                <explanation-step></explanation-step>
            </tab-content>
            <tab-content title="Pacchetto" icon="ti-money">
                <SelectPackageStep @selectPackageHandler = "onSelectPackage" ></SelectPackageStep>
            </tab-content>
            <tab-content title="Informazione" icon="ti-info">
                <PersonalInfoStep ref="personalInfoStep"></PersonalInfoStep>
            </tab-content>

            <template slot="footer" slot-scope="props">
                <div class=wizard-footer-left>
                    <wizard-button  v-if="props.activeTabIndex > 0 && !props.isLastStep" @click.native="props.prevTab()" :style="props.fillButtonStyle">Previous</wizard-button>
                    </div>
                    <div class="wizard-footer-right">
                    <wizard-button v-if="!props.isLastStep && props.activeTabIndex != 2" @click.native="props.nextTab()" class="wizard-footer-right" :style="props.fillButtonStyle">Next</wizard-button>
                    <wizard-button v-else-if="props.isLastStep" class="wizard-footer-right finish-button" @click.native="finishSteps" :style="props.fillButtonStyle">Done</wizard-button>
                </div>
            </template>
        </form-wizard>

</template>

<script>
    import {FormWizard, TabContent} from 'vue-form-wizard';
    import NumberInputSpinner from 'vue-number-input-spinner';

    import ExplanationStep from './ExplanationStep.vue';
    import SelectPackageStep from './SelectPackageStep.vue';
    import PersonalInfoStep from './PersonalInfoStep.vue';

    export default {
        props: ['seller_id'],
        components: {
            ExplanationStep,
            SelectPackageStep,
            PersonalInfoStep,
            NumberInputSpinner,
            FormWizard,
            TabContent
        },
        data: ()=>{
            return {
            form :{
                age: 18,
                packagePrice:0,
                personalInfo: {},
                agePrice: 0
            }
          }
        },
        methods: {
            onComplete: function(){
                this.form.agePrice = this.priceFromAge(this.form.age);
                console.log(this.form);
            },
            ageStepValidate: function(){
                return this.form.age >= 18 && this.form.age <= 59;
            },
            onSelectPackage: function(price){
                this.form.packagePrice = price;
                this.$refs.formWizard.nextTab();
            },
            finishSteps: function(){
                if(this.$refs.personalInfoStep.validateUser())
                {
                    this.form.personalInfo = this.$refs.personalInfoStep.form;
                    this.$refs.formWizard.$emit('on-complete');
                }
            },
            priceFromAge: function(number){
                if (number == 18){
                    return "300.000";
                }
                if (number == 19){
                    return "286.807";
                }
                if (number == 20){
                    return "273.390";
                }
                if (number == 21){
                    return "259.665";
                }
                if (number == 22){
                    return "245.633";
                }
                if (number == 23){
                    return "231.839";
                }
                if (number == 24){
                    return "217.707";
                }
                if (number == 25){
                    return "203.343";
                }
                if (number == 26){
                    return "189.314";
                }
                if (number == 27){
                    return "175.918";
                }
                if (number == 28){
                    return "162.748";
                }
                if (number == 29){
                    return "150.653";
                }
                if (number == 30){
                    return "139.104";
                }
                if (number == 31){
                    return "128.388";
                }
                if (number == 32){
                    return "118.389";
                }
                if (number == 33){
                    return "109.223";
                }
                if (number == 34){
                    return "100.604";
                }
                if (number == 35){
                    return "92.688";
                }
                if (number == 36){
                    return "85.389";
                }
                if (number == 37){
                    return "78.644";
                }
                if (number == 38){
                    return "72.464";
                }
                if (number == 39){
                    return "66.725";
                }
                if (number == 40){
                    return "61.459";
                }
                if (number == 41){
                    return "56.597";
                }
                if (number == 42){
                    return "52.120";
                }
                if (number == 43){
                    return "47.974";
                }
                if (number == 44){
                    return "44.126";
                }
                if (number == 45){
                    return "40.584";
                }
                if (number == 46){
                    return "37.313";
                }
                if (number == 47){
                    return "34.816";
                }
                if (number == 48){
                    return "32.439";
                }
                if (number == 49){
                    return "30.187";
                }
                if (number == 50){
                    return "28.048";
                }
                if (number == 51){
                    return "26.020";
                }
                if (number == 52){
                    return "24.114";
                }
                if (number == 53){
                    return "22.323";
                }
                if (number == 54){
                    return "20.648";
                }
                if (number == 55){
                    return "19.080";
                }
                if (number == 56){
                    return "17.620";
                }
                if (number == 57){
                    return "16.255";
                }
                if (number == 58){
                    return "14.985";
                }
                if (number == 59){
                    return "13.801";
                }
            }
    }
  }
</script>
