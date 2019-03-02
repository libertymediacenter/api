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
import { LibraryEntity } from '../../library.entity';
import { SeasonEntity } from './season.entity';
import { IgnoreProperty, Property } from '@tsed/common';
import { ISeries } from '../../../interfaces/media';

@Entity({name: 'series'})
export class SeriesEntity implements ISeries {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  @IgnoreProperty()
  uuid: string;

  @Column('text')
  @Property()
  title: string;

  @Column('text')
  slug: string;

  @Column('text', {unique: true})
  path: string;

  @Column('integer', {name: 'start_year', nullable: true})
  @Property()
  startYear: number;

  @Column('integer', {name: 'end_year', nullable: true})
  @Property()
  endYear: number | null;

  @Column('text', {nullable: true})
  @Property()
  tagline: string;

  @Column('text', {nullable: true})
  @Property()
  summary: string;

  @Column('text', {nullable: true})
  @Property()
  network: string;

  @Column('integer', {nullable: true})
  @Property()
  runtime: number;

  @Column('integer', {name: 'tvdb_id', nullable: true})
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

  @OneToMany(type => LibraryEntity, library => library.series)
  @JoinColumn({name: 'library_uuid', referencedColumnName: 'uuid'})
  library: LibraryEntity;

  @ManyToOne(type => SeasonEntity, season => season.series)
  seasons: SeasonEntity[];
}
