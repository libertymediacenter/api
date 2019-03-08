import {
  Column,
  CreateDateColumn,
  Entity,
  JoinColumn,
  ManyToOne,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { SeriesEntity } from './series.entity';
import { EpisodeEntity } from './episode.entity';
import { IgnoreProperty, Property } from '@tsed/common';

@Entity({name: 'seasons'})
export class SeasonEntity {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  @IgnoreProperty()
  uuid: string;

  @Column('integer')
  @Property()
  number: number;

  @Column('integer', {name: 'start_year', nullable: true})
  @Property()
  startYear: number;

  @Column('integer', {name: 'end_year', nullable: true})
  @Property()
  endYear: number | null;

  @Column('integer', {name: 'the_tv_db_id', nullable: true})
  @Property()
  theTvDbId: number;

  @Column('text', {name: 'imdb_id', nullable: true})
  @Property()
  imdbId: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @ManyToOne(type => SeriesEntity, series => series.seasons)
  @JoinColumn({name: 'series_uuid', referencedColumnName: 'uuid'})
  series: SeriesEntity;

  @OneToMany(type => EpisodeEntity, episode => episode.season)
  episodes: EpisodeEntity[];
}
