import Vue from 'vue';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (hash_id,type,id)=> (
  new Promise((resolve, reject) => {

    Vue.$http.get(`/commissions/show-list-send/${hash_id}/${type}/${id}`)
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);