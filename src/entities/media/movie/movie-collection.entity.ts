import {
  Column,
  CreateDateColumn,
  Entity,
  Index, JoinColumn,
  ManyToOne,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { LibraryEntity } from '../../library.entity';
import { MovieEntity } from './movie.entity';

@Entity({name: 'movie_collection'})
@Index('movie_collection_slug_library_uuid_key', ['slug', 'library'], {unique: true})
export class MovieCollectionEntity {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('bigint', {name: 'tmdb_id'})
  tmdbId: number;

  @Column('citext')
  name: string;

  @Column('citext')
  slug: string;

  @Column('text', {nullable: true})
  poster?: string;

  @Column('text', {nullable: true})
  backdrop?: string;

  @CreateDateColumn({name: 'created_at'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at'})
  updatedAt: Date;

  @ManyToOne(type => LibraryEntity, library => library.movies)
  @JoinColumn({name: 'library_uuid', referencedColumnName: 'uuid'})
  library: LibraryEntity;

  @OneToMany(type => MovieEntity, movie => movie.collection)
  movies: MovieEntity[];
}
