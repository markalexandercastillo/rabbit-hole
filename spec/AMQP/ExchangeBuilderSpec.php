<?php
use kahlan\plugin\Stub;
use kahlan\Arg;
use Kastilyo\RabbitHole\Spec\Helper;
use Kastilyo\RabbitHole\AMQP\ExchangeBuilder;

describe('ExchangeBuilder', function () {
    beforeEach(function () {
        Helper::initializeAMQPStubs();
        $this->connection = Helper::getAMQPConnection();
        $this->exchange_builder = new ExchangeBuilder($this->connection);
    });

    context('->build', function () {
        beforeEach(function () {
            $this->exchange_name = 'some_exchange';
            $this->exchange_builder->setName($this->exchange_name);
        });

        it("makes the connection if it hasn't been made yet", function () {
            expect($this->connection)
                ->toReceive('connect');
            $this->exchange_builder->build();
        });

        it("doesn't make the connection if it's been made already", function () {
            Stub::on($this->connection)
                ->method('isConnected')
                ->andReturn(true);
            expect($this->connection)
                ->not
                ->toReceive('connect');
            $this->exchange_builder->build();
        });

        context('Exchange declaration', function () {
            it('sets the name of the exchange', function () {
                expect('AMQPExchange')
                    ->toReceive('setName')
                    ->with($this->exchange_name);
                $this->exchange_builder->build();
            });

            it("sets the exchange to be of type 'topic'", function () {
                expect('AMQPExchange')
                    ->toReceive('setType')
                    ->with(AMQP_EX_TYPE_TOPIC);
                $this->exchange_builder->build();
            });

            it('sets the exchange as durable', function () {
                expect('AMQPExchange')
                    ->toReceive('setFlags')
                    ->with(AMQP_DURABLE);
                $this->exchange_builder->build();
            });

            it('declares the exchange', function () {
                expect('AMQPExchange')
                    ->toReceive('declareExchange');
                $this->exchange_builder->build();
            });
        });
    });
});