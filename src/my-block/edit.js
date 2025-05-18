import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';
import './style.scss';

export default function Edit() {
  return (
    <div {...useBlockProps()}>
      <p>{__('Hello from My Block 👋', 'ripcurl')}</p>
    </div>
  );
}
