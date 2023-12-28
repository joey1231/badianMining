import Vue from 'vue';

const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (model, hash_id) => (
  new Promise((resolve, reject) => {
   Vue.$http.post(`/public/validate/${hash_id}`,model)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);