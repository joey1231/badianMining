import Vue from 'vue';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id)=> (
  new Promise((resolve, reject) => {

    Vue.$http.post(`/commissions/running-save/${hash_id}`,{})
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);