import { Controller, Get, PathParams, QueryParams } from '@tsed/common';
import { AudioCodec, Preset, TranscodeService, VideoCodec, VideoProfile } from '../../services/transcode.service';

@Controller('/transcode')
export class TranscodeController {

  constructor(private transcodeService: TranscodeService) {
  }

  @Get('/:librarySlug/:videoSlug')
  public async get(@PathParams('librarySlug') librarySlug: string,
                   @PathParams('videoSlug') videoSlug: string,
                   // Query Params
                   @QueryParams('startTime') startTime: number,
                   @QueryParams('audioBitrate') audioBitrate: number,
                   @QueryParams('audioCodec') audioCodec: string,
                   @QueryParams('audioChannels') audioChannels: number,
                   @QueryParams('videoBitrate') videoBitrate: number,
                   @QueryParams('videoCodec') videoCodec: string) {

    const videoPath = '/Users/martin/Movies/Unfriended (2015)/Unfriended (2014) Bluray-1080p.mkv';

    const transcode = await this.transcodeService.start({
      startTime,
      segmentDuration: 10,
      filePath: videoPath,
      outputDirectory: `${__dirname}/../../../public/transcode`,
      qualityOptions: {
        audio: {
          codec: AudioCodec[audioCodec] || AudioCodec.AAC,
          bitrate: audioBitrate || 192,
          channels: audioChannels || 2,
        },
        video: {
          codec: VideoCodec[videoCodec] || VideoCodec.X264,
          bitrate: videoBitrate || 1000,
          profile: VideoProfile.MAIN,
        },
        preset: Preset.ULTRAFAST,
      },
      type: 'transcode-request',
    });

    const absPath = transcode.playlistPath.split('/');
    const publicPath = absPath.slice(absPath.length - 3).join('/');

    return {
      playlistUrl: `/${publicPath}`,
    };
  }

}
