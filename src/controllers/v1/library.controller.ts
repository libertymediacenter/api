import { AfterRoutesInit, BodyParams, Controller, Get, Post, Status } from '@tsed/common';
import { Name, Summary } from '@tsed/swagger';
import { LibraryEntity } from '../../entities/library.entity';
import { ILibrary } from '../../interfaces/library';
import { LibraryService } from '../../services/library.service';
import { LibraryScanController } from './library-scan.controller';

@Name('Library')
@Controller('/libraries', LibraryScanController)
export class LibraryController {

  constructor(private libraryService: LibraryService) {
  }

  @Get('')
  @Summary('Get libraries')
  public get(): Promise<[LibraryEntity[], number]> {
    return this.libraryService.findAll();
  }

  @Post('')
  @Status(201)
  @Summary('Create a library')
  public create(@BodyParams() library: LibraryEntity): Promise<LibraryEntity> {
    return this.libraryService.create(library);
  }

}
