<template>
  <form-wizard @on-complete="onComplete" title="hello" subtitle="die">
            <tab-content title="Age" icon="ti-user" :before-change="ageStepValidate">
                <div class="container justify-content-center align-items-center align-content-center align-self-center" id="app-head" style="background-image:url(&quot;assets/img/team-motivation-teamwork-together-53958.jpeg&quot;);background-position:center;background-size:cover;background-repeat:no-repeat;">
                    <h4 class="text-center text-head" style="font-family:Montserrat, sans-serif;font-style:normal;font-weight:bold;font-size:15px;padding-top:21px;color:rgb(230,230,230);">Ognuno di noi ha tante cose a cui tiene,<br></h4>
                    <div class="row text-center">
                        <img src="assets/img/LogoneBlalloNice.png" id="center-img-head" style="width:50px;">
                    </div>
                    <h4 class="text-center text-head" style="font-weight:bold;font-family:Montserrat, sans-serif;font-size:16px;color:rgb(255,255,255);padding-top:-36px;">proteggile tutte con un unica soluzione.<br></h4>
                </div>
                <div class="container">
                    <p class="para-data" style="color:rgba(33,37,41,0.84);">Scegli il prodotto più completo che garantisca Tutela e Risparmio.&nbsp;</p>
                    <p class="para-data data-calco">Inserisci letà e ottieni la tua soluzione</p>
                    <div class = "age-wrapper">
                        <NumberInputSpinner :min="18" :max="99" :integerOnly="true" v-model="form.age" />
                    </div>
                </div>
            </tab-content>
            <tab-content title="Additional Info" icon="ti-settings">
                <explanation-step></explanation-step>
            </tab-content>
            <tab-content title="Last step" icon="ti-check">
                <SelectPackageStep></SelectPackageStep>
            </tab-content>
            <tab-content title="Last step"
                            icon="ti-check">
                Yuhuuu! This seems pretty damn simple
            </tab-content>
        </form-wizard>

</template>

<script>
    import {FormWizard, TabContent} from 'vue-form-wizard';
    import NumberInputSpinner from 'vue-number-input-spinner';

    import ExplanationStep from './ExplanationStep.vue';
    import SelectPackageStep from './SelectPackageStep.vue';

    export default {
        components: {
            ExplanationStep,
            SelectPackageStep,
            NumberInputSpinner,
            FormWizard,
            TabContent
        },
      data: ()=>{
          return {
            form :{
                age: 18
            }
          }
      },
     methods: {
        onComplete: function(){
            alert('Yay. Done!');
       },
       ageStepValidate: function(){
           return this.form.age >= 18 && this.form.age <= 99;
       }
      }
  }
</script>
