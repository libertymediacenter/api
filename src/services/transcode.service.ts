import { Service } from '@tsed/di';
import { Event } from 'cote';
import * as cote from 'cote';

@Service()
export class TranscodeService {
  private _requester: cote.Requester;

  constructor() {
    this._requester = new cote.Requester({
      name: 'Transcode Requester Service',
      key: 'transcode',
      namespace: 'internal',
    });
  }

  public async start(request: TranscodeRequest): Promise<TranscodeResponse> {
    console.log(request);

    const res = await  this._requester.send<TranscodeRequest>(request);

    console.log(res);

    return res;
  }
}

export enum AudioCodec {
  AAC = 'aac',
}

export enum VideoCodec {
  X264 = 'x264',
}

export enum VideoResolution {
  HD2160 = 2160,
  HD1440 = 1440,
  HD1080P = 1080,
  HD720P = 720,
  SD576P = 576,
  SD432P = 432,
  SD360P = 360
}

export enum Preset {
  ULTRAFAST = 'ultrafast',
  SUPERFAST = 'superfast',
  VERYFAST = 'veryfast',
  FASTER = 'faster',
  FAST = 'fast',
  MEDIUM = 'medium',
  SLOW = 'slow',
  SLOWER = 'slower',
  VERYSLOW = 'veryslow',
}

export enum VideoProfile {
  BASELINE = 'baseline',
  MAIN = 'main',
  HIGH10 = 'high10',
  HIGH422 = 'high422',
  HIGH444 = 'high444'
}

export interface TranscodeRequest extends Event {
  startTime: number;
  segmentDuration: number;
  filePath: string;
  outputDirectory: string;
  qualityOptions: {
    audio: {
      codec: AudioCodec;
      bitrate: number; // kbps
      channels: number;
    };
    video: {
      codec: VideoCodec;
      bitrate: number; // kbps
      profile: VideoProfile;
    };
    preset: Preset;
  },
}

export interface TranscodeResponse {
  playlistPath: string;
}