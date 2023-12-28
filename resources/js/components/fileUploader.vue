<template>
  <div>
    <dropzone :id="id"  url="/upload" @vdropzone-error="errorUpload" @vdropzone-success="uploadSuccess"  :max-file-size-in-m-b="200" @vdropzone-removed-file="removedfile" :options="dropOptions">
         <input type="hidden" name="_token" :value="token">
    </dropzone>
  </div>
</template>

<script>
  import Dropzone from 'vue2-dropzone'
  import store from './../store/index.js';
  export default {
    name: 'fileuploader',
    props:{
      token: {
        type: String,
        required: true
      },
      id: {
        type: String,
        required: true
      },
    },
    data(){
     return {
      dropOptions: {
        maxFilesize: 5, // MB
        url: this.url,
        _token: this.token,
        maxFile:1
      }
     } 
    },
    components: {
      Dropzone
    },
    methods: {
      uploadSuccess(file){
        console.log(file);
        store.dispatch('setUploadFilePath',JSON.parse(file.xhr.response).path);
        store.dispatch('setImportObject', null);
      },
      removedfile(file,error,xhr){
       for(var i =0 ; i < this.$parent[this.modelname][this.name].length; i++){
          store.dispatch('setUploadFile',{url:'','path':''});
       }
       //console.log(this.$parent[this.modelname][this.name]);
      },
     errorUpload(file, message, xhr) {
     
      if(message['imported'] != undefined) {
         store.dispatch('setImportObject', message['imported']);
      }
      
     }
    }
  }
</script>