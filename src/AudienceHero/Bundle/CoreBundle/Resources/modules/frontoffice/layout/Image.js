import React from 'react';
import PropTypes from 'prop-types';
import queryString from 'query-string';

const imgEndpoint = '/img';

export function buildImgSrc({ url, size, crop }) {
    const params = { url, size, crop };
    const q = queryString.stringify(params);
    return `${imgEndpoint}?${q}`;
}

export class Image extends React.Component {
    render() {
        return (
            <img
                src={buildImgSrc({
                    url: this.props.src,
                    size: this.props.size,
                    crop: this.props.crop,
                })}
                style={this.props.style}
            />
        );
    }
}

Image.propTypes = {
    src: PropTypes.string.isRequired,
    alt: PropTypes.string,
    crop: PropTypes.string,
    size: PropTypes.string.isRequired,
    style: PropTypes.object,
};

Image.defaultProps = {
    style: {},
    alt: '',
};

export default Image;
