import { Controller, Get } from '@tsed/common';
import { TranscodeService } from '../../services/transcode.service';

export interface HlsInitResponse {
  m3u8: string;
}

@Controller('/stream')
export class HlsController {

  constructor(private transcodeService: TranscodeService) {
  }

  @Get('')
  public async getStream() {
    const videoPath = '/Users/martin/Movies/Unfriended (2015)/Unfriended (2014) Bluray-1080p.mkv';
    const transcodePath = `transcode`; // relative - for full prepend: ${__dirname}/../../../public

  }
}
