import axios from 'axios';

axios.defaults.headers.common.Accept = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

axios.interceptors.request.use(
  function onHttpRequestInterceptorFulfilled(config) {
    console.debug(`http:req(${config.url ?? '?'})`, config);

    return config;
  },
  async function onHttpRequestInterceptorRejected(error) {
    console.error(error);

    throw error;
  },
);

axios.interceptors.response.use(
  function onHttpResponseInterceptorFulfilled(response) {
    console.debug(
      `http:res(${response.config.url ?? '?'}, ${response.status})`,
      response,
    );

    return response;
  },
  async function onHttpResponseInterceptorRejected(error) {
    console.error(error);

    throw error;
  },
);

export default axios;
