import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default schemaBuilder => (
  new Promise((resolve, reject) => {
   Vue.$http.post(`/advisers`,schemaBuilder)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);