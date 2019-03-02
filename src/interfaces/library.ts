import { LibraryType } from '../entities/library.entity';

export interface ILibrary {
  title: string;
  type: LibraryType;
  metadataLang: string;
  path: string;
}
