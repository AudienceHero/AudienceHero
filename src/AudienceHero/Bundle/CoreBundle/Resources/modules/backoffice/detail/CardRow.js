import React, { Component, Children } from 'react';
import { Row } from 'react-flexbox-grid';

export class CardRow extends Component {
    render() {
        const { record, resource, basePath, children } = this.props;
        return (
            <Row style={{ marginBottom: '1em' }}>
                {Children.map(children, cardField => {
                    return React.cloneElement(cardField, {
                        record,
                        resource,
                        basePath,
                    });
                })}
            </Row>
        );
    }
}

export default CardRow;
