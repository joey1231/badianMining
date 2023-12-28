import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default schemaBuilder => (
  new Promise((resolve, reject) => {
   Vue.$http.post(`/users`,schemaBuilder)
    .then((response) => {
      success(response, resolve);
    },(error)=>{
      failed(error, reject);
    })
    .catch((error) => {
      console.log(error);
      failed(error, reject);
    });
  })
);