import Axios, { AxiosInstance, AxiosRequestConfig } from 'axios';

export class HttpClient {
  private static _axios: AxiosInstance;

  private constructor() {
    if (!HttpClient._axios) {
      HttpClient._axios = Axios.create();
    }
  }

  public static getInstance() {
   if (!HttpClient._axios) {
     HttpClient._axios = Axios.create();
   }

   return HttpClient._axios;
  }

}
