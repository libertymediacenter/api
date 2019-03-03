import {
  Column,
  CreateDateColumn,
  Entity, ManyToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { MovieEntity } from './movie.entity';
import { PersonEntity } from '../person.entity';

@Entity({name: 'movie_cast'})
export class MovieCastEntity {

  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('citext')
  role: string;

  @Column('int')
  order: number;

  @ManyToOne(type => MovieEntity)
  movie: MovieEntity;

  @ManyToOne(type => PersonEntity)
  person: PersonEntity;

  @CreateDateColumn({name: 'created_at'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at'})
  updatedAt: Date;

}
