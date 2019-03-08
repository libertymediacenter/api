import { Property } from '@tsed/common';
import {
  Column,
  CreateDateColumn,
  Entity, Index, JoinColumn, ManyToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { MovieEntity } from './movie.entity';
import { PersonEntity } from '../../person.entity';

@Entity({name: 'movie_cast'})
@Index('movie_cast_role_movie_person_key', ['role', 'movie', 'person'], {unique: true})
@Index('movie_cast_movie_uuid_fkey', ['movie'])
@Index('movie_cast_person_uuid_fkey', ['person'])
export class MovieCastEntity {

  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('citext')
  @Property()
  role: string;

  @Column('int')
  @Property()
  order: number;

  @ManyToOne(type => MovieEntity)
  @JoinColumn({name: 'movie_uuid'})
  movie: MovieEntity;

  @ManyToOne(type => PersonEntity, {eager: true})
  @JoinColumn({name: 'person_uuid'})
  @Property()
  person: PersonEntity;

  @CreateDateColumn({name: 'created_at'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at'})
  updatedAt: Date;

}
