import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id,schemaBuilder) => (
  new Promise((resolve, reject) => {
   Vue.$http.put(`/workflows/${hash_id}`,schemaBuilder)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);