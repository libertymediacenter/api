import { FFProbeResult, Format, Stream } from '../interfaces/ffprobe.interfaces';
import * as ffmpeg from 'fluent-ffmpeg';

export const ffprobe = (filePath: string): Promise<FFProbeResult> => {
  return new Promise(((resolve, reject) => {
    ffmpeg(filePath)
      .ffprobe(((err, data) => {
        if (err) reject(err);

        const result: FFProbeResult = {
          streams: data.streams as Stream[],
          format: data.format as Format,
          chapters: data.chapters,
        };

        resolve(result);
      }));
  }));
};