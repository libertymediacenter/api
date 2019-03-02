import { AxiosRequestConfig } from 'axios';
import * as got from 'got';

export const getMimeType = (config: AxiosRequestConfig): Promise<string> => {

  return new Promise<string>(((resolve, reject) => {
    got.get(config.url, {
      timeout: 10000,
      throwHttpErrors: true,
      query: config.params || '',
    }).then((res) => {
      resolve(res.headers['content-type']);
    }).catch(reject);
  }));
};

export const generateImageStreamRequest = (config: AxiosRequestConfig): AxiosRequestConfig => {
  const request: AxiosRequestConfig = {
    method: 'GET',
    responseType: 'stream',
  };

  Object.assign(request, config);

  return request;
};
