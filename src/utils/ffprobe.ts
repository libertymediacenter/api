import { FFProbeResult, Format, Stream } from '../interfaces/ffprobe.interfaces';
import * as ffmpeg from 'fluent-ffmpeg';

export const ffprobe = (filePath: string): Promise<FFProbeResult> => {
  return new Promise(((resolve, reject) => {
    ffmpeg(filePath)
      .ffprobe(((err, data) => {
        if (err) reject(err);
        if (data === undefined || data.streams === undefined || data.format === undefined || data.chapters === undefined) {
          reject(new Error('Unknown error'));
        } else {
          const result: FFProbeResult = {
            streams: data.streams as Stream[],
            format: data.format as Format,
            chapters: data.chapters,
          };

          resolve(result);
        }

        resolve(null);
      }));
  }));
};