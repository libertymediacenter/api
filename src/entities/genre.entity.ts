import { JsonProperty, Required, Schema } from '@tsed/common';
import { Column, CreateDateColumn, Entity, ManyToMany, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm';
import { MovieEntity } from './media/movie.entity';
import { SeriesEntity } from './media/tv/series.entity';
import { EbookEntity } from './media/book/ebook.entity';

@Schema({title: 'genre'})
@Entity({
  name: 'genres', orderBy: {
    name: 'ASC',
  },
})
export class GenreEntity {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('citext')
  @JsonProperty()
  @Required()
  name: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @ManyToMany(type => MovieEntity)
  movies?: MovieEntity[];

  @ManyToMany(type => SeriesEntity)
  series?: SeriesEntity[];

  @ManyToMany(type => EbookEntity)
  ebooks?: EbookEntity[];
}
