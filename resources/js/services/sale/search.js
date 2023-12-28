import Vue from 'vue';
import helpers from './../../utils/helper.js';
const success = (response, resolve) => {
  resolve(response);
};

const failed = (error, reject) => {
  reject(error);
};

export default (params)=> (
  new Promise((resolve, reject) => {

    Vue.$http.get(helpers.buildUrl(`/sales/search`, params))
    .then((response) => {
      success(response, resolve);
    })
    .catch((error) => {
      failed(error, reject);
    });
  })
);