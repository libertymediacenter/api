import {
  Column,
  CreateDateColumn,
  Entity,
  JoinColumn, ManyToOne,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { SeasonEntity } from './season.entity';
import { IgnoreProperty, Property, Schema } from '@tsed/common';
import { Description } from '@tsed/swagger';

@Schema({title: 'episode'})
@Entity({name: 'episodes'})
export class EpisodeEntity {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  @IgnoreProperty()
  uuid: string;

  @Column('text')
  @Property()
  title: string;

  @Column('text')
  @Property()
  slug: string;

  @Column('text', {unique: true})
  path: string;

  @Column('date', {name: 'air_date', nullable: true})
  @Property()
  airDate?: Date;

  @Column('integer', {nullable: true})
  @Property()
  @Description('runtime in minutes')
  runtime?: number;

  @Column('integer', {name: 'the_tv_db_id', nullable: true})
  @Property()
  theTvDbId?: number;

  @Column('text', {name: 'imdb_id', nullable: true})
  @Property()
  imdbId?: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @OneToMany(type => SeasonEntity, season => season.episodes)
  @JoinColumn({name: 'season_id'})
  season: SeasonEntity;
}
