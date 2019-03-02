import { Property, Required, Schema } from '@tsed/common';
import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm';

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
}
