import Vue from 'vue';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (ids)=> (
  new Promise((resolve, reject) => {

    Vue.$http.post(`/commissions/record/delete-records`,{ids: ids})
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);