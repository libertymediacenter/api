import {
  Column,
  CreateDateColumn,
  Entity,
  JoinColumn,
  ManyToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { MovieEntity } from './movie.entity';

@Entity({name: 'movie_media'})
export class MovieMediaEntity {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @ManyToOne(type => MovieEntity)
  @JoinColumn({name: 'movie_uuid'})
  movie: MovieEntity;

  @Column('text')
  path: string;

  @Column('bigint')
  size: number;

  @Column('int')
  height: number;

  @Column('int')
  width: number;

  @Column('int')
  bitrate: number;

  @CreateDateColumn({name: 'created_at'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at'})
  updatedAt: Date;
}
