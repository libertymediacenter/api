import { Controller, Get, PathParams } from '@tsed/common';
import * as fse from 'fs-extra';
import * as sharp from 'sharp';
import { sleep } from '../../utils/sleep';

const publicDir = `${__dirname}/../../../public`;

@Controller('/images')
export class ImageController {

  @Get('/:size/:file')
  public async getImage(@PathParams('size') size: string,
                        @PathParams('file') file: string) {
    const path = `${publicDir}/images/${size}/${file}`;

    try {
      await fse.stat(path);
    } catch (e) {

      const sizeDir = `${publicDir}/images/${size}`;
      try {
        await fse.stat(sizeDir);
      } catch (e) {
        await fse.mkdir(sizeDir);
      }

      const widthHeight = size.split('x');

      const width = Number(widthHeight[0]);
      const height = Number(widthHeight[1]);

      const resized = await sharp(await fse.readFile(`${publicDir}/images/${file}`))
        .resize(width, height)
        .toBuffer();

      await fse.writeFile(path, resized);

      await sleep(200);
    }

    // when the request reaches here, the server automatically serves the file.
  }

}
