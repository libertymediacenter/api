import {
  Column,
  CreateDateColumn,
  Entity, JoinTable,
  ManyToMany,
  ManyToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { EbookAttributeEntity } from './ebook-attribute.entity';
import { GenreEntity } from '../../genre.entity';

@Entity({name: 'ebooks'})
export class EbookEntity {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  uuid: string;

  @Column('text')
  title: string;

  @Column('integer')
  pages: number;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @ManyToMany(type => GenreEntity)
  @JoinTable({name: 'ebook_genre'})
  genres: GenreEntity[];

  @ManyToOne(type => EbookAttributeEntity, ebookAttribute => ebookAttribute.ebook)
  attributes: EbookAttributeEntity[];
}
