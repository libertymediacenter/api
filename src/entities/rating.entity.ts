import { JsonProperty, Property, Required, Schema } from '@tsed/common';
import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm';
import { IRating } from '../interfaces/media';

@Schema({title: 'rating'})
@Entity({name: 'ratings'})
export class RatingEntity implements IRating {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('text')
  @Property()
  @Required()
  provider: string;

  @Column('text', {name: 'display_score'})
  @Property()
  @Required()
  displayScore: string;

  @Column('integer')
  @Property()
  @Required()
  score: number;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;
}
