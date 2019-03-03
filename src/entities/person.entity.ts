import { Property, Required, Schema } from '@tsed/common';
import {
  Column,
  CreateDateColumn,
  Entity,
  ManyToMany,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { MovieEntity } from './media/movie.entity';
import { MovieCastEntity } from './media/movie-cast.entity';

@Schema({title: 'person'})
@Entity({name: 'persons'})
export class PersonEntity {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column()
  @Property()
  @Required()
  name: string;

  @Column('text', {unique: true})
  slug: string;

  @Column('text', {nullable: true})
  @Property()
  photo: string;

  @Column('text', {nullable: true})
  @Property()
  bio: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @OneToMany(type => MovieCastEntity, movieCastEntity => movieCastEntity.person)
  movieRoles: MovieCastEntity[];
}
