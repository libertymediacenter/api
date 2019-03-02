import { Controller, MergeParams, PathParams, Post } from '@tsed/common';
import { Name, Summary } from '@tsed/swagger';
import { LibraryScannerService } from '../../services/library-scanner.service';
import { LibraryService } from '../../services/library.service';

@Name('Library')
@Controller('/:slug/scan')
@MergeParams()
export class LibraryScanController {

  constructor(private libraryScannerService: LibraryScannerService,
              private libraryService: LibraryService) {
  }

  @Post('')
  @Summary('Scan library')
  public async scan(@PathParams('slug') slug: string) {
    const library = await this.libraryService.findBySlug(slug);

    return this.libraryScannerService.scanLibraryByUuid(library.uuid);
  }

}
