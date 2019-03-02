import { AxiosRequestConfig } from 'axios';
import { PathLike } from 'fs';
import { mkdirs, createWriteStream } from 'fs-extra';
import { resolve } from 'path';
import { Stream } from 'stream';
import { HttpClient } from '../services/http-client';

export enum StorageDir {
  IMAGES = 'images',
}

const basePath = `${__dirname}/../../public`;

const getRelativePath = (absolutePath: PathLike) => {
  const path = absolutePath.toString();
  const removeCount = resolve(basePath).length + 1;

  return path.substr(removeCount);
};

const axios = HttpClient.getInstance();

/**
 * Write a stream to the public path
 * @param name
 * @param dir
 * @param request
 */
export const streamToFile = async (name: string, dir: StorageDir, request: AxiosRequestConfig): Promise<string> => {
  const dirPath = `${basePath}/${dir}`;
  const path = resolve(dirPath, name);
  const config: AxiosRequestConfig = {
    responseType: 'stream',
    timeout: 10000,
  };

  Object.assign(config, request);

  return new Promise<string>((async (resolve, reject) => {
    try {
      await mkdirs(dirPath);
    } catch (e) {
      reject(e);
    }
    try {
      const res = await axios.get<Stream>(request.url, request);
      const writer = createWriteStream(path);

      res.data.pipe(writer);

      res.data.on('end', () => {
        writer.on('finish', () => {
          writer.end();
          resolve(getRelativePath(path));
        });
      });

      res.data.on('error', (e) => {
        reject(e);
      });

    } catch (e) {
      reject(e);
    }

  }));

};
